<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Course;
use App\Models\CourseOutcome;
use App\Models\ProgramAssignment;
use App\Models\Submission;
use App\Models\SubmissionRequirement;
use App\Support\AcademicTerm;
use App\Support\FileLinks;
use Illuminate\Http\Request;

// Course Filing: for each course taught in a term, the files faculty submitted for their
// program's per-course requirements (Submissions and Deadline), and the Secretary's check.
// The Program Head's files in Course Oversight are reference material and are not counted.
class CourseFilingController extends Controller
{
    public function index(Request $request)
    {
        $program = $request->get('program');
        $schoolYear = $request->get('school_year', AcademicTerm::currentSchoolYear());
        $semester = $request->get('semester', AcademicTerm::currentSemester());
        $isCurrentTerm = $schoolYear === AcademicTerm::currentSchoolYear() && $semester === AcademicTerm::currentSemester();

        // Courses with a faculty member assigned in the term.
        $courseIds = ProgramAssignment::where('school_year', $schoolYear)->where('semester', $semester)->pluck('course_id');
        $courses = Course::whereIn('id', $courseIds)
            ->when($program, fn($q) => $q->where('program', $program))
            ->orderBy('program')
            ->curriculumOrder()
            ->get();

        // The term's per-course requirements; older ones without a term follow the current term.
        $requirementsByProgram = SubmissionRequirement::where('per_course', true)
            ->whereIn('program', $courses->pluck('program')->unique())
            ->where(function ($q) use ($schoolYear, $semester, $isCurrentTerm) {
                $q->where(fn($t) => $t->where('school_year', $schoolYear)->where('semester', $semester));
                if ($isCurrentTerm) {
                    $q->orWhereNull('school_year');
                }
            })
            ->orderBy('deadline')
            ->get()
            ->groupBy('program');

        $submissionsByCourse = Submission::whereIn('requirement_id', $requirementsByProgram->flatten()->pluck('id'))
            ->whereIn('course_id', $courses->pluck('id'))
            ->with(['faculty:id,name', 'checker:id,name'])
            ->get()
            ->groupBy('course_id');

        $outcomesByCourse = CourseOutcome::whereIn('course_id', $courses->pluck('id'))
            ->with('programOutcomes')
            ->get()
            ->groupBy('course_id');

        $filing = $courses->map(function ($course) use ($requirementsByProgram, $submissionsByCourse, $outcomesByCourse) {
            $submissions = $submissionsByCourse->get($course->id, collect());

            // Two columns per course: what faculty submitted (Submission) and what the
            // Secretary has checked and validated.
            $documents = [];
            foreach ($requirementsByProgram->get($course->program, collect()) as $requirement) {
                $file = $submissions->firstWhere('requirement_id', $requirement->id);
                $documents[] = [
                    'type'     => $requirement->id,
                    'label'    => $requirement->title,
                    'deadline' => $requirement->deadline->format('M d, Y'),
                    'status'   => !$file ? 'missing' : ($file->filing_status ?? 'pending'),
                    'files'    => $file ? [[
                        'id'            => $file->id,
                        'title'         => $file->file_name,
                        'version'       => $file->status === 'late' ? 'Late' : 'On time',
                        'file_type'     => strtoupper($file->file_type),
                        'uploader'      => $file->faculty?->name ?? 'a former faculty member',
                        'uploaded_at'   => ($file->submitted_at ?? $file->created_at)->format('M d, Y'),
                        'status'        => $file->filing_status ?? 'pending',
                        'checker'       => $file->checker?->name,
                        'checked_at'    => $file->filing_checked_at?->format('M d, Y g:i A'),
                        'return_reason' => $file->filing_return_reason,
                        'view_url'      => FileLinks::view('submission', $file->id),
                    ]] : [],
                ];
            }

            // OBE alignment: the course must actually have Course Outcomes defined, and
            // every one of them mapped to at least one Program Outcome (Course Oversight's
            // CO-PO mapping). An unmapped CO means that part of the course's contribution
            // to the program's outcomes was never recorded, so it doesn't count yet.
            $outcomes = $outcomesByCourse->get($course->id, collect());
            $hasObeAlignment = $outcomes->isNotEmpty() && $outcomes->every(fn($co) => $co->programOutcomes->isNotEmpty());

            $submitted = collect($documents)->where('status', '!=', 'missing')->count();
            $validated = collect($documents)->where('status', 'validated')->count();
            $needed = count($documents) + 1; // + OBE alignment

            return [
                'id'                => $course->id,
                'code'              => $course->code,
                'title'             => $course->title,
                'program'           => $course->program,
                'documents'         => $documents,
                'submitted'         => $submitted,
                'validated'         => $validated,
                'to_check'          => collect($documents)->where('status', 'pending')->count(),
                'has_obe_alignment' => $hasObeAlignment,
                // Complete only counts files the Secretary has validated, not just submitted.
                'completion_pct'    => round((($validated + ($hasObeAlignment ? 1 : 0)) / $needed) * 100),
            ];
        });

        $totals = [
            'courses'   => $filing->count(),
            'to_check'  => $filing->sum('to_check'),
            'complete'  => $filing->where('completion_pct', 100)->count(),
        ];

        $schoolYears = collect(AcademicTerm::schoolYearOptions())
            ->merge(ProgramAssignment::distinct()->pluck('school_year'))
            ->push($schoolYear)->unique()->sort()->values();

        return view('secretary.course-filing', compact('filing', 'program', 'totals', 'schoolYear', 'semester', 'schoolYears'));
    }

    // Validate a file, return it to the faculty member with a reason, or undo the check.
    public function review(Request $request, Submission $submission)
    {
        abort_unless($submission->course_id && $submission->requirement?->per_course, 404);

        $data = $request->validate([
            'action' => 'required|in:validate,return,reset',
            'reason' => 'required_if:action,return|nullable|string|max:500',
        ], [
            'reason.required_if' => 'Please write the reason for returning this file so the faculty member knows what to fix.',
        ]);

        $course = $submission->course;
        $where = "{$submission->requirement->title} \"{$submission->file_name}\" of {$course->code}";

        if ($data['action'] === 'reset') {
            $submission->update([
                'filing_status'        => null,
                'filing_checked_by'    => null,
                'filing_checked_at'    => null,
                'filing_return_reason' => null,
            ]);
            AuditLog::record('Course Filing Updated', "Undid the check on {$where}");
            $message = 'The file is back to "waiting for your check".';
        } else {
            $returned = $data['action'] === 'return';
            $submission->update([
                'filing_status'        => $returned ? Submission::FILING_RETURNED : Submission::FILING_VALIDATED,
                'filing_checked_by'    => auth()->id(),
                'filing_checked_at'    => now(),
                'filing_return_reason' => $returned ? trim($data['reason']) : null,
            ]);
            AuditLog::record(
                $returned ? 'Course File Returned' : 'Course File Validated',
                ($returned ? "Returned {$where}: " . trim($data['reason']) : "Checked and validated {$where}")
            );
            $message = $returned
                ? "Returned to {$submission->faculty?->name} with your reason."
                : 'Marked as checked and validated.';
        }

        return back()->with('success', $message)->with('open_course', $course->id);
    }
}
