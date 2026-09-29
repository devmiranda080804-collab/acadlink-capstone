<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\CourseTopic;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamSection;
use App\Models\ProgramAssignment;
use App\Services\QuestionGeneratorService;
use App\Support\AcademicTerm;
use App\Support\BloomLevels;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
                ])->values());

            return [$assignment->id => $topics];
        });

        return view('faculty.exam-generator', compact('assignments', 'exams', 'topicsByAssignment'));
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

        $exam = Exam::create([
            'program_assignment_id' => $assignment->id,
            'faculty_id'            => auth()->id(),
            'title'                 => $request->title,
            'grading_period'        => $request->grading_period,
            'duration_minutes'      => $request->duration_minutes,
            'target_items'          => $request->target_items,
        ]);

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
        abort_if($exam->isFinalized(), 403, 'This exam is already finalized and can no longer be edited.');

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

            foreach ($request->input('sections', []) as $sectionIndex => $sectionData) {
                $section = $exam->sections()->create([
                    'title'        => $sectionData['title'],
                    'instructions' => $sectionData['instructions'] ?? null,
                    'order'        => $sectionIndex,
                ]);

                foreach ($sectionData['questions'] ?? [] as $questionIndex => $questionData) {
                    $this->createQuestion($section->id, null, $questionData, $questionIndex);
                }
            }
        });

        return response()->json($exam->load('sections.questions.children'));
    }

    protected function createQuestion(int $sectionId, ?int $parentId, array $data, int $order): void
    {
        $question = ExamQuestion::create([
            'exam_section_id' => $sectionId,
            'parent_id'       => $parentId,
            'type'            => $data['type'],
            'topic'           => $data['topic'] ?? null,
            // Bloom's Level is never taken from the client (Panel comment #11 — auto-filled
            // by predefined logic, manual input prevented). Case Analysis containers carry none.
            'bloom_level'     => BloomLevels::bloomFor($data['type']),
            'question_text'   => $data['question_text'],
            'points'          => $data['points'],
            'options'         => $data['options'] ?? null,
            'order'           => $order,
        ]);

        foreach ($data['children'] ?? [] as $childIndex => $childData) {
            $this->createQuestion($sectionId, $question->id, $childData, $childIndex);
        }
    }

    public function tos(Exam $exam)
    {
        abort_unless($exam->faculty_id === auth()->id(), 403);

        $exam->load('sections.questions.children');

        return response()->json($exam->tosBreakdown());
    }

    // TOS Generator preview (Panel comment #8: Total Hours auto-fetched from OBE data,
    // no manual input) — computed on the fly from CourseTopic, nothing is persisted here.
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

        return response()->json(
            CourseTopic::targetBreakdown($assignment->course_id, $request->grading_period, (int) $request->total_items)
        );
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
    // faculty review/edit/delete drafts in the builder just like manually-typed ones,
    // and Bloom's Level is always re-assigned from $type on save, never from the AI).
    public function generateQuestions(Request $request)
    {
        $request->validate([
            'course_topic_id' => 'required|exists:course_topics,id',
            'type'             => ['required', Rule::in(QuestionGeneratorService::SUPPORTED_TYPES)],
            'count'            => 'required|integer|min:1|max:10',
        ]);

        $topic = CourseTopic::whereIn('course_id', $this->assignedCourseIds())
            ->where('id', $request->course_topic_id)
            ->firstOrFail();

        if (! $topic->notes && ! $topic->module_path) {
            return response()->json([
                'message' => 'Add teaching notes or upload a module for this topic first — Course Coordination → Topics & Hours.',
            ], 422);
        }

        $modulePath = $topic->module_path
            ? \Illuminate\Support\Facades\Storage::disk('public')->path($topic->module_path)
            : null;

        try {
            $drafts = (new QuestionGeneratorService())->generate(
                $request->type, $topic->topic, $topic->notes ?? '', (int) $request->count, $modulePath
            );
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Anthropic\Core\Exceptions\AnthropicException $e) {
            return response()->json(['message' => 'AI generation failed: ' . $e->getMessage()], 502);
        }

        $bloom = BloomLevels::bloomFor($request->type);

        return response()->json([
            'questions' => collect($drafts)->map(fn($d) => [
                'type'          => $request->type,
                'topic'         => $topic->topic,
                'question_text' => $d['question_text'],
                'options'       => $d['options'],
                'points'        => 1,
                'bloom_level'   => $bloom, // display only — re-derived server-side on save regardless
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
