<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ProgramAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamGeneratorController extends Controller
{
    protected function currentSchoolYear(): string
    {
        $now = now();
        $year = $now->year;
        return $now->month >= 8 ? $year . '-' . ($year + 1) : ($year - 1) . '-' . $year;
    }

    protected function currentSemester(): string
    {
        $month = now()->month;
        if ($month >= 8 && $month <= 12) return 'First Semester';
        if ($month >= 1 && $month <= 5)  return 'Second Semester';
        return 'Summer';
    }

    public function index()
    {
        $facultyId = auth()->id();

        // Courses this faculty is assigned to teach this term — the "Subject" choices in the builder
        $assignments = ProgramAssignment::with('course')
            ->where('faculty_id', $facultyId)
            ->where('school_year', $this->currentSchoolYear())
            ->where('semester', $this->currentSemester())
            ->get();

        $exams = Exam::with('programAssignment.course')
            ->where('faculty_id', $facultyId)
            ->latest()
            ->get();

        return view('faculty.exam-generator', compact('assignments', 'exams'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_assignment_id' => 'required|exists:program_assignments,id',
            'title'                 => 'required|string|max:255',
            'grading_period'        => 'required|in:Prelim,Midterm,Final',
            'duration_minutes'      => 'nullable|integer|min:1',
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
            'sections.*.questions.*.type'          => 'required|string|max:50',
            'sections.*.questions.*.topic'         => 'nullable|string|max:255',
            'sections.*.questions.*.bloom_level'   => 'nullable|string|max:50',
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
            'bloom_level'     => $data['bloom_level'] ?? null,
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
}
