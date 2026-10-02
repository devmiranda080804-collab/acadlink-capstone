<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\CourseTopic;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamSection;
use App\Models\ProgramAssignment;
use App\Models\Tos;
use App\Models\TosTopic;
use App\Services\QuestionGeneratorService;
use App\Services\TosDocumentBuilder;
use App\Support\AcademicTerm;
use App\Support\BloomLevels;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use PhpOffice\PhpWord\IOFactory;

class ExamGeneratorController extends Controller
{
    public function index()
    {
        $facultyId = auth()->id();

        // Courses this faculty is assigned to teach this term — the "Subject" choices in the builder
        $assignments = ProgramAssignment::with('course')
            ->where('faculty_id', $facultyId)
            ->where('school_year', AcademicTerm::currentSchoolYear())
            ->where('semester', AcademicTerm::currentSemester())
            ->get();

        $exams = Exam::with('programAssignment.course')
            ->where('faculty_id', $facultyId)
            ->latest()
            ->get();

        // OBTL topics for every assigned course, grouped by grading period — embedded
        // so the TOS Generator / Exam Builder can populate their Topic dropdowns without
        // extra round trips. Keyed by program_assignment_id since that's what the
        // Subject dropdown selects.
        $topicsByAssignment = $assignments->mapWithKeys(function ($assignment) {
            $topics = CourseTopic::where('course_id', $assignment->course_id)
                ->orderBy('order')
                ->get()
                ->groupBy('grading_period')
                ->map(fn($group) => $group->map(fn($t) => [
                    'id' => $t->id, 'topic' => $t->topic, 'hours' => $t->hours,
                    // Lets the AI Generate modal warn immediately if a topic has nothing
                    // for the AI to read from, instead of only failing after submit.
                    'has_content' => (bool) ($t->notes || $t->module_path),
                ])->values());

            return [$assignment->id => $topics];
        });

        // Which grading periods already have a persisted TOS, per assignment — lets
        // the New Exam modal warn upfront (before submit) that a subject + grading
        // period combo needs its TOS generated first, instead of only failing after
        // the faculty member clicks Create.
        $tosByAssignment = $assignments->mapWithKeys(function ($assignment) {
            $periods = Tos::where('program_assignment_id', $assignment->id)
                ->pluck('grading_period');

            return [$assignment->id => $periods];
        });

        return view('faculty.exam-generator', compact('assignments', 'exams', 'topicsByAssignment', 'tosByAssignment'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_assignment_id' => 'required|exists:program_assignments,id',
            'title'                 => 'required|string|max:255',
            'grading_period'        => 'required|in:Prelim,Midterm,Final',
            'duration_minutes'      => 'nullable|integer|min:1',
            'target_items'          => 'nullable|integer|min:1',
        ]);

        // Faculty can only build exams for courses actually assigned to them
        $assignment = ProgramAssignment::where('id', $request->program_assignment_id)
            ->where('faculty_id', auth()->id())
            ->firstOrFail();

        // The exam's own content (topics, item counts, Bloom's level
        // breakdown) is supposed to be built FROM the TOS, not the other way
        // around — so the TOS for this subject + grading period must already
        // exist (Generate TOS in the TOS Generator tab) before an exam can start.
        $tosExists = Tos::where('program_assignment_id', $assignment->id)
            ->where('grading_period', $request->grading_period)
            ->exists();

        abort_unless($tosExists, 422, 'Generate the Table of Specifications (TOS) for this subject and grading period first — the exam is built from it.');

        // A faculty member should only ever have one unfinished (draft) exam
        // per subject + grading period at a time — without this, clicking
        // "+ New Exam" again (or a double-click) silently piles up empty
        // duplicate drafts instead of resuming the one already started.
        $existingDraft = Exam::where('program_assignment_id', $assignment->id)
            ->where('faculty_id', auth()->id())
            ->where('grading_period', $request->grading_period)
            ->where('status', 'draft')
            ->first();

        if ($existingDraft) {
            return response()->json($existingDraft->load('programAssignment.course'), 200);
        }

        $exam = Exam::create([
            'program_assignment_id' => $assignment->id,
            'faculty_id'            => auth()->id(),
            'title'                 => $request->title,
            'grading_period'        => $request->grading_period,
            'duration_minutes'      => $request->duration_minutes,
            'target_items'          => $request->target_items,
        ]);

        // Link this exam to its originating TOS, if one was generated for the same
        // program assignment + grading period and isn't already linked to another exam.
        Tos::where('program_assignment_id', $assignment->id)
            ->where('grading_period', $request->grading_period)
            ->whereNull('exam_id')
            ->update(['exam_id' => $exam->id]);

        return response()->json($exam->load('programAssignment.course'), 201);
    }

    public function show(Exam $exam)
    {
        abort_unless($exam->faculty_id === auth()->id(), 403);

        $exam->load(['sections.questions.children', 'programAssignment.course']);

        return response()->json($exam);
    }

    public function update(Request $request, Exam $exam)
    {
        abort_unless($exam->faculty_id === auth()->id(), 403);
        // Finalizing marks the exam ready for printing/export — it no longer locks out
        // further edits, so faculty can still fix things afterward.

        $request->validate([
            'title'                        => 'required|string|max:255',
            'duration_minutes'             => 'nullable|integer|min:1',
            'sections'                     => 'array',
            'sections.*.title'             => 'required|string|max:255',
            'sections.*.instructions'      => 'nullable|string',
            'sections.*.questions'         => 'array',
            'sections.*.questions.*.type'          => ['required', Rule::in(array_keys(BloomLevels::TYPES))],
            'sections.*.questions.*.topic'         => 'nullable|string|max:255',
            'sections.*.questions.*.question_text' => 'required|string',
            'sections.*.questions.*.points'        => 'required|integer|min:1',
            'sections.*.questions.*.options'       => 'nullable|array',
            'sections.*.questions.*.bloom_level'   => ['nullable', Rule::in(BloomLevels::LEVELS)],
            'sections.*.questions.*.children'      => 'array',
        ]);

        DB::transaction(function () use ($request, $exam) {
            $exam->update([
                'title'            => $request->title,
                'duration_minutes' => $request->duration_minutes,
            ]);

            // Sections/questions are replaced wholesale on every save — the builder
            // always sends its full in-memory structure, there's no partial-patch mode
            $exam->sections()->delete();

            // TOS breakdown for this exam's target_items, keyed by topic name, so each
            // question's Bloom's Level can be resolved from its position within its topic
            // (see createQuestion()) instead of a fixed per-type mapping.
            $levelsByTopic = collect();
            if ($exam->target_items) {
                $breakdown = CourseTopic::targetBreakdown(
                    $exam->programAssignment->course_id,
                    $exam->grading_period,
                    (int) $exam->target_items
                );
                $levelsByTopic = collect($breakdown['topics'])->keyBy('topic');
            }
            $topicPositions = [];

            foreach ($request->input('sections', []) as $sectionIndex => $sectionData) {
                $section = $exam->sections()->create([
                    'title'        => $sectionData['title'],
                    'instructions' => $sectionData['instructions'] ?? null,
                    'order'        => $sectionIndex,
                ]);

                foreach ($sectionData['questions'] ?? [] as $questionIndex => $questionData) {
                    $this->createQuestion($section->id, null, $questionData, $questionIndex, $levelsByTopic, $topicPositions);
                }
            }
        });

        return response()->json($exam->load('sections.questions.children'));
    }

    protected function createQuestion(int $sectionId, ?int $parentId, array $data, int $order, $levelsByTopic, array &$topicPositions): void
    {
        // Bloom's Level: a value the faculty explicitly picked (validated above as one of
        // the 6 real levels — never free text) is trusted and kept as-is, so a manual
        // override actually sticks instead of being silently recomputed away. Otherwise
        // it's resolved from this question's position within its topic's own running
        // count, against the TOS's per-level breakdown — e.g. the 1st-5th question under a
        // topic land under Remembering, the 6th-10th under Understanding, matching the
        // TOS's own numbering, regardless of the question's format/type (instructor's
        // requirement: exam items must align to both the TOS and Bloom's Taxonomy). Case
        // Analysis containers carry no level of their own. The per-topic position still
        // advances for every question regardless of whether it was manually set, so
        // later auto-resolved questions in the same topic don't skip/repeat a slot.
        $bloomLevel = null;
        if ($data['type'] !== 'case-analysis') {
            $topic = $data['topic'] ?? null;
            $position = null;
            if ($topic) {
                $topicPositions[$topic] = ($topicPositions[$topic] ?? 0) + 1;
                $position = $topicPositions[$topic];
            }

            if (!empty($data['bloom_level'])) {
                $bloomLevel = $data['bloom_level'];
            } elseif ($topic && $levelsByTopic->has($topic)) {
                $bloomLevel = CourseTopic::resolveBloomLevelForPosition($levelsByTopic->get($topic), $position);
            } else {
                // No topic assigned, or the topic has no TOS data (e.g. exam wasn't
                // started from a TOS target) — fall back to the type's own default level.
                $bloomLevel = BloomLevels::bloomFor($data['type']);
            }
        }

        $question = ExamQuestion::create([
            'exam_section_id' => $sectionId,
            'parent_id'       => $parentId,
            'type'            => $data['type'],
            'topic'           => $data['topic'] ?? null,
            'bloom_level'     => $bloomLevel,
            'question_text'   => $data['question_text'],
            'points'          => $data['points'],
            'options'         => $data['options'] ?? null,
            'order'           => $order,
        ]);

        foreach ($data['children'] ?? [] as $childIndex => $childData) {
            $this->createQuestion($sectionId, $question->id, $childData, $childIndex, $levelsByTopic, $topicPositions);
        }
    }

    public function tos(Exam $exam)
    {
        abort_unless($exam->faculty_id === auth()->id(), 403);

        $exam->load('sections.questions.children');

        return response()->json($exam->tosBreakdown());
    }

    // TOS Generator preview (Panel comment #8: Total Hours auto-fetched from OBE data,
    // no manual input). Every generate also persists a Tos/TosTopic snapshot — matching
    // the manuscript's ERD, which models TableOfSpecifications as a stored entity, not a
    // purely on-the-fly computation.
    public function tosTarget(Request $request)
    {
        $request->validate([
            'program_assignment_id' => 'required|exists:program_assignments,id',
            'grading_period'        => 'required|in:Prelim,Midterm,Final',
            'total_items'           => 'required|integer|min:1',
        ]);

        $assignment = ProgramAssignment::where('id', $request->program_assignment_id)
            ->where('faculty_id', auth()->id())
            ->firstOrFail();

        $breakdown = CourseTopic::targetBreakdown($assignment->course_id, $request->grading_period, (int) $request->total_items);

        $this->persistTos($assignment, $request->grading_period, $breakdown);

        return response()->json($breakdown);
    }

    // Saves (or replaces) the persisted TOS snapshot for this program assignment +
    // grading period — called from both tosTarget() (auto-computed) and tosDownload()
    // (possibly faculty-edited), so the stored record always reflects the latest version.
    private function persistTos(ProgramAssignment $assignment, string $gradingPeriod, array $breakdown): Tos
    {
        $tos = Tos::updateOrCreate(
            ['program_assignment_id' => $assignment->id, 'grading_period' => $gradingPeriod],
            [
                'total_hours'  => $breakdown['total_hours'] ?? 0,
                'total_items'  => $breakdown['total_items'] ?? 0,
                'total_points' => $breakdown['total_points'] ?? 0,
                'created_by'   => auth()->id(),
            ]
        );

        $tos->topics()->delete();
        foreach ($breakdown['topics'] as $topic) {
            TosTopic::create([
                'tos_id'          => $tos->id,
                'course_topic_id' => $topic['id'] ?? null,
                'topic'           => $topic['topic'] ?? '',
                'hours'           => $topic['hours'] ?? 0,
                'weight_percent'  => $topic['weight_percent'] ?? 0,
                'target_items'    => $topic['target_items'] ?? 0,
                'topic_points'    => $topic['topic_points'] ?? 0,
                'levels'          => $topic['levels'] ?? [],
            ]);
        }

        return $tos;
    }

    // Downloadable, editable .docx matching the official TOS template — faculty fill in
    // the schedule, sign, or adjust wording after downloading; nothing round-trips back.
    // Takes the breakdown exactly as shown on screen (the client mirrors targetBreakdown()'s
    // numbering/rounding rules in JS), so any Edit-mode adjustments the faculty made are
    // reflected in the downloaded document instead of being silently recomputed away.
    public function tosDownload(Request $request)
    {
        $request->validate([
            'program_assignment_id' => 'required|exists:program_assignments,id',
            'grading_period'        => 'required|in:Prelim,Midterm,Final',
            'breakdown'             => 'required|string',
        ]);

        $assignment = ProgramAssignment::with('course')
            ->where('id', $request->program_assignment_id)
            ->where('faculty_id', auth()->id())
            ->firstOrFail();

        $breakdown = json_decode($request->breakdown, true);

        if (!is_array($breakdown) || empty($breakdown['topics']) || !is_array($breakdown['topics'])) {
            abort(422, 'Invalid Table of Specification data. Please regenerate and try again.');
        }

        // Defensive defaults so a partial/edited payload never crashes the document builder.
        foreach ($breakdown['topics'] as &$topic) {
            $topic['topic'] = (string) ($topic['topic'] ?? '');
            $topic['hours'] = (int) ($topic['hours'] ?? 0);
            $topic['weight_percent'] = $topic['weight_percent'] ?? 0;
            $topic['target_items'] = (int) ($topic['target_items'] ?? 0);
            foreach (BloomLevels::LEVELS as $level) {
                $topic['levels'][$level] = array_merge(
                    ['count' => 0, 'range' => null, 'points' => 0, 'points_per_item' => 1],
                    is_array($topic['levels'][$level] ?? null) ? $topic['levels'][$level] : []
                );
            }
        }
        unset($topic);
        $breakdown['total_hours'] = (int) ($breakdown['total_hours'] ?? 0);
        $breakdown['total_items'] = (int) ($breakdown['total_items'] ?? 0);
        $breakdown['total_points'] = (int) ($breakdown['total_points'] ?? array_sum(array_column($breakdown['topics'], 'topic_points')));

        $this->persistTos($assignment, $request->grading_period, $breakdown);

        $phpWord = (new TosDocumentBuilder())->build($assignment->course, $request->grading_period, $breakdown, auth()->user());

        $filename = 'TOS-' . $assignment->course->code . '-' . $request->grading_period . '.docx';

        return response()->streamDownload(function () use ($phpWord) {
            IOFactory::createWriter($phpWord, 'Word2007')->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);
    }

    public function finalize(Exam $exam)
    {
        abort_unless($exam->faculty_id === auth()->id(), 403);
        abort_if($exam->isFinalized(), 422, 'This exam is already finalized.');

        $exam->update([
            'status'       => 'finalized',
            'finalized_at' => now(),
        ]);

        return back()->with('success', 'Exam finalized. You can now export and print it for signing.');
    }

    public function destroy(Exam $exam)
    {
        abort_unless($exam->faculty_id === auth()->id(), 403);
        abort_if($exam->isFinalized(), 403, 'Finalized exams cannot be deleted.');

        $exam->delete();

        return back()->with('success', 'Exam deleted.');
    }

    // AI-assisted question drafting (human-in-the-loop — nothing is persisted here,
    // faculty review/edit/delete drafts in the builder just like manually-typed ones).
    // Faculty still picks the question FORMAT ($type) for the whole batch, but each
    // item's Bloom's Level is resolved from its position within the topic against the
    // TOS breakdown — e.g. if the topic's TOS says positions 1-5 are Remembering and
    // 6-10 are Understanding, and the faculty already has 4 questions for this topic and
    // asks for 3 more (all Multiple Choice, say), positions 5-7 resolve to Remembering,
    // Remembering, Understanding — so the 3 generated MC questions land at those levels
    // respectively (display only; re-derived server-side from position again on save).
    public function generateQuestions(Request $request)
    {
        $request->validate([
            'course_topic_id' => 'required|exists:course_topics,id',
            'type'            => ['required', Rule::in(QuestionGeneratorService::SUPPORTED_TYPES)],
            'count'           => 'required|integer|min:1|max:10',
            'existing_count'  => 'nullable|integer|min:0',
            'target_items'    => 'nullable|integer|min:0',
        ]);

        $topic = CourseTopic::whereIn('course_id', $this->assignedCourseIds())
            ->where('id', $request->course_topic_id)
            ->firstOrFail();

        if (! $topic->notes && ! $topic->module_path) {
            return response()->json([
                'message' => 'Add teaching notes or upload a module for this topic first — Course Coordination → Topics & Hours.',
            ], 422);
        }

        // Only PDFs are fed to Claude as a document — DOCX modules already had their
        // text extracted into $topic->notes at upload time (see storeModuleFile()
        // in CourseCoordinationController), so there's nothing further to attach here.
        $modulePath = ($topic->module_path && str_ends_with(strtolower($topic->module_path), '.pdf'))
            ? \Illuminate\Support\Facades\Storage::disk('public')->path($topic->module_path)
            : null;

        $topicRow = null;
        if ($request->target_items) {
            $breakdown = CourseTopic::targetBreakdown($topic->course_id, $topic->grading_period, (int) $request->target_items);
            $topicRow = collect($breakdown['topics'])->firstWhere('topic', $topic->topic);
        }

        $existingCount = (int) ($request->existing_count ?? 0);
        $count = (int) $request->count;

        // Resolve each new question's Bloom's Level from its position within the topic,
        // continuing after whatever's already been added for it.
        $levels = [];
        for ($p = $existingCount + 1; $p <= $existingCount + $count; $p++) {
            $levels[] = CourseTopic::resolveBloomLevelForPosition($topicRow, $p);
        }
        $bloomLevelsForPrompt = array_values(array_unique(array_filter($levels)));

        try {
            $drafts = (new QuestionGeneratorService())->generate(
                $request->type, $topic->topic, $topic->notes ?? '', $count, $modulePath, $bloomLevelsForPrompt
            );
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Anthropic\Core\Exceptions\AnthropicException $e) {
            return response()->json(['message' => 'AI generation failed: ' . $e->getMessage()], 502);
        }

        $fallbackBloom = BloomLevels::bloomFor($request->type);

        return response()->json([
            'questions' => collect($drafts)->values()->map(fn($d, $i) => [
                'type'          => $request->type,
                'topic'         => $topic->topic,
                'question_text' => $d['question_text'],
                'options'       => $d['options'],
                'points'        => 1,
                'bloom_level'   => $levels[$i] ?? $fallbackBloom, // display only — re-derived server-side on save regardless
            ])->all(),
        ]);
    }

    // Courses this faculty is actually assigned to teach this term — used for the
    // security check on generateQuestions()'s course_topic_id.
    protected function assignedCourseIds()
    {
        return ProgramAssignment::where('faculty_id', auth()->id())
            ->where('school_year', AcademicTerm::currentSchoolYear())
            ->where('semester', AcademicTerm::currentSemester())
            ->pluck('course_id');
    }

    // ─── Item Bank ──────────────────────────────────────────────────
    // A view over this faculty's own past questions — no separate storage, so there's
    // nothing to keep in sync when the original question is edited or deleted.

    public function bankItems(Request $request)
    {
        $request->validate(['course_id' => 'required|exists:courses,id']);

        $items = ExamQuestion::whereNull('parent_id')
            ->whereHas('section.exam', function ($q) use ($request) {
                $q->where('faculty_id', auth()->id())
                    ->whereHas('programAssignment', fn($pa) => $pa->where('course_id', $request->course_id));
            })
            ->with('children')
            ->latest()
            ->get();

        return response()->json($items);
    }

    public function reuseItem(Request $request, ExamQuestion $question)
    {
        // The source question must belong to one of this faculty's own exams
        abort_unless($question->section->exam->faculty_id === auth()->id(), 403);

        $request->validate(['exam_section_id' => 'required|exists:exam_sections,id']);

        // The target section must also belong to one of this faculty's own exams
        $targetSection = ExamSection::findOrFail($request->exam_section_id);
        abort_unless($targetSection->exam->faculty_id === auth()->id(), 403);

        $clone = $this->cloneQuestion($question, $targetSection->id, null);

        return response()->json($clone->load('children'), 201);
    }

    protected function cloneQuestion(ExamQuestion $source, int $targetSectionId, ?int $targetParentId): ExamQuestion
    {
        $nextOrder = ExamQuestion::where('exam_section_id', $targetSectionId)
            ->where('parent_id', $targetParentId)
            ->max('order') + 1;

        $clone = ExamQuestion::create([
            'exam_section_id' => $targetSectionId,
            'parent_id'       => $targetParentId,
            'type'            => $source->type,
            'topic'           => $source->topic,
            'bloom_level'     => $source->bloom_level,
            'question_text'   => $source->question_text,
            'points'          => $source->points,
            'options'         => $source->options,
            'order'           => $nextOrder,
        ]);

        foreach ($source->children as $child) {
            $this->cloneQuestion($child, $targetSectionId, $clone->id);
        }

        return $clone;
    }
}
