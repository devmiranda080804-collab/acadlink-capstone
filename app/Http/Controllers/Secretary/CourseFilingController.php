<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\CourseOutcome;
use App\Support\FileLinks;
use Illuminate\Http\Request;

class CourseFilingController extends Controller
{
    public function index(Request $request)
    {
        $program = $request->get('program');

        $courses = Course::when($program, fn($q) => $q->where('program', $program))
            ->orderBy('program')
            ->curriculumOrder()
            ->get();

        $materialsByCourse = CourseMaterial::whereIn('course_id', $courses->pluck('id'))
            ->whereIn('type', array_keys(CourseMaterial::FILING_DOCUMENTS))
            ->with(['uploader:id,name', 'checker:id,name'])
            ->latest()
            ->get()
            ->groupBy('course_id');

        $outcomesByCourse = CourseOutcome::whereIn('course_id', $courses->pluck('id'))
            ->with('programOutcomes')
            ->get()
            ->groupBy('course_id');

        $filing = $courses->map(function ($course) use ($materialsByCourse, $outcomesByCourse) {
            $materials = $materialsByCourse->get($course->id, collect());

            // Two columns per course: what was submitted (filed by the Program Head in
            // Course Oversight) and what the Secretary has checked and validated.
            $documents = [];
            foreach (CourseMaterial::FILING_DOCUMENTS as $type => $label) {
                $files = $materials->where('type', $type)->values();
                $documents[] = [
                    'type'   => $type,
                    'label'  => $label,
                    'status' => $this->documentStatus($files),
                    'files'  => $files->map(fn($m) => [
                        'id'            => $m->id,
                        'title'         => $m->title,
                        'version'       => $m->version,
                        'file_type'     => strtoupper($m->file_type),
                        'uploader'      => $m->uploader?->name ?? 'Unknown',
                        'uploaded_at'   => $m->created_at->format('M d, Y'),
                        'status'        => $m->filing_status ?? 'pending',
                        'checker'       => $m->checker?->name,
                        'checked_at'    => $m->filing_checked_at?->format('M d, Y g:i A'),
                        'return_reason' => $m->filing_return_reason,
                        'view_url'      => FileLinks::view('material', $m->id),
                    ])->all(),
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
                'to_check'          => $materials->whereNull('filing_status')->count(),
                'has_obe_alignment' => $hasObeAlignment,
                // Complete only counts files the Secretary has validated, not just uploaded.
                'completion_pct'    => round((($validated + ($hasObeAlignment ? 1 : 0)) / $needed) * 100),
            ];
        });

        $totals = [
            'courses'   => $filing->count(),
            'to_check'  => $filing->sum('to_check'),
            'complete'  => $filing->where('completion_pct', 100)->count(),
        ];

        return view('secretary.course-filing', compact('filing', 'program', 'totals'));
    }

    // Validate a file, return it to the uploader with a reason, or undo the check.
    public function review(Request $request, CourseMaterial $material)
    {
        abort_unless(array_key_exists($material->type, CourseMaterial::FILING_DOCUMENTS), 404);

        $data = $request->validate([
            'action' => 'required|in:validate,return,reset',
            'reason' => 'required_if:action,return|nullable|string|max:500',
        ], [
            'reason.required_if' => 'Please write the reason for returning this file so the uploader knows what to fix.',
        ]);

        $course = $material->course;
        $label = CourseMaterial::FILING_DOCUMENTS[$material->type];
        $where = "{$label} \"{$material->title}\" of {$course->code}";

        if ($data['action'] === 'reset') {
            $material->update([
                'filing_status'        => null,
                'filing_checked_by'    => null,
                'filing_checked_at'    => null,
                'filing_return_reason' => null,
            ]);
            AuditLog::record('Course Filing Updated', "Undid the check on {$where}");
            $message = 'The file is back to "waiting for your check".';
        } else {
            $returned = $data['action'] === 'return';
            $material->update([
                'filing_status'        => $returned ? CourseMaterial::FILING_RETURNED : CourseMaterial::FILING_VALIDATED,
                'filing_checked_by'    => auth()->id(),
                'filing_checked_at'    => now(),
                'filing_return_reason' => $returned ? trim($data['reason']) : null,
            ]);
            AuditLog::record(
                $returned ? 'Course File Returned' : 'Course File Validated',
                ($returned ? "Returned {$where}: " . trim($data['reason']) : "Checked and validated {$where}")
            );
            $message = $returned
                ? "Returned to {$material->uploader?->name} with your reason."
                : 'Marked as checked and validated.';
        }

        return back()->with('success', $message)->with('open_course', $course->id);
    }

    // missing: nothing filed; returned: a file was sent back and nothing newer was filed
    // yet; validated: every current file is validated; pending: still waiting to be checked.
    protected function documentStatus($files): string
    {
        if ($files->isEmpty()) {
            return 'missing';
        }

        // A returned file stops counting once a newer file of the same document is filed.
        $current = $files->reject(fn($m) => $m->is_returned
            && $files->contains(fn($other) => $other->created_at->gt($m->created_at)));

        if ($current->contains(fn($m) => $m->is_returned)) {
            return 'returned';
        }

        return $current->every(fn($m) => $m->is_validated) ? 'validated' : 'pending';
    }
}
