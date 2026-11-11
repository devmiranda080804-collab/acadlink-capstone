<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\SubmissionRequirement;
use App\Models\Submission;
use App\Services\AcademicDocumentValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function index()
    {
        $faculty = auth()->user();
        $myPrograms = $faculty->programCodes();

        $requirements = SubmissionRequirement::with(['submissions.faculty', 'submissions.checker'])
            ->whereIn('program', $myPrograms)
            ->orderByPriority()
            ->get()
            ->map(function ($req) use ($faculty) {
                // A per-course requirement needs one file for each course the faculty member
                // teaches in its term; whoever teaches the course may submit it.
                $courses = $req->per_course
                    ? $req->courses($faculty)->map(fn ($course) => [
                        'course'     => $course,
                        'submission' => $req->submissions->firstWhere('course_id', $course->id),
                    ])->values()
                    : collect();
                $mySubmission = $req->per_course ? null : $req->submissions->firstWhere('faculty_id', $faculty->id);
                // Shared faculty: a file sent for the same kind of requirement in another of
                // their programs already counts here (group count, panel comment #27).
                $coveredBy = $req->per_course || $mySubmission ? null : $req->groupSubmissionFor($faculty);

                $missing = $req->per_course
                    ? $courses->whereNull('submission')->count()
                    : ($mySubmission || $coveredBy ? 0 : 1);
                $returned = $req->per_course
                    ? $courses->filter(fn ($c) => $c['submission']?->is_returned && $c['submission']->faculty_id === $faculty->id)->count()
                    : (int) ($mySubmission?->is_returned ?? false);

                return [
                    'id'          => $req->id,
                    'title'       => $req->title,
                    'description' => $req->description,
                    'type'        => SubmissionRequirement::typeLabel($req->type),
                    'type_key'    => $req->type,
                    'file_types'  => $req->fileTypesLabel(),
                    'accept'      => $req->acceptAttribute(),
                    'priority'    => $req->priority,
                    'program'     => $req->program,
                    'deadline'    => $req->deadline,
                    'days_left'   => $req->days_left,
                    'is_due_soon' => $req->is_due_soon,
                    'per_course'  => $req->per_course,
                    'term'        => $req->termLabel(),
                    'courses'     => $courses,
                    'submission'  => $mySubmission,
                    'covered_by'  => $coveredBy,
                    // Done once every course (or their own file) has something submitted.
                    'done'        => $missing === 0 && (!$req->per_course || $courses->isNotEmpty()),
                    'missing'     => $missing,
                    'returned'    => $returned,
                ];
            });

        return view('faculty.submissions', compact('requirements', 'myPrograms'));
    }

    public function store(Request $request, SubmissionRequirement $requirement)
    {
        $faculty = auth()->user();
        abort_unless($faculty->belongsToProgram($requirement->program), 403);

        $request->validate([
            'file'      => 'required|file|mimes:' . implode(',', $requirement->allowedExtensions()) . '|max:20480',
            'course_id' => $requirement->per_course ? 'required|integer' : 'nullable',
        ], [
            'course_id.required' => 'Choose the course this file is for.',
            'file.mimes'         => 'This requirement accepts ' . $requirement->fileTypesLabel() . ' files only.',
        ]);

        $courseId = null;
        if ($requirement->per_course) {
            $course = $requirement->courses($faculty)->firstWhere('id', (int) $request->course_id);
            abort_unless((bool) $course, 403, 'You can only submit for courses you teach this term.');
            $courseId = $course->id;

            // One file per course is enough; only the faculty member who sent it can replace it.
            $existing = Submission::with('faculty')->where('requirement_id', $requirement->id)->where('course_id', $courseId)->first();
            if ($existing && $existing->faculty_id !== $faculty->id) {
                return back()->withErrors([
                    'file' => ($existing->faculty->name ?? 'Another faculty member') . ' already submitted this for ' . $course->code . ', so it is covered.',
                ]);
            }
        }

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        $key = $requirement->per_course
            ? ['requirement_id' => $requirement->id, 'course_id' => $courseId]
            : ['requirement_id' => $requirement->id, 'faculty_id' => $faculty->id, 'course_id' => null];
        $previous = Submission::where($key)->first();

        $hash = hash_file('sha256', $file->getRealPath());
        if ($duplicate = $this->duplicateMessage($requirement, $faculty->id, $previous?->id, $hash, $file->getClientOriginalName())) {
            return back()->withErrors(['file' => $duplicate]);
        }

        // Content sanity-check against what this requirement actually expects
        // (e.g. a "TOS" requirement shouldn't accept an unrelated file) — runs
        // on the uploaded temp file, before anything is stored.
        $check = (new AcademicDocumentValidator())->validate($file->getRealPath(), $extension, $requirement->type);
        if (!$check['valid']) {
            return back()->withErrors([
                'file' => ($check['reason'] ?? 'This file does not appear to match what "' . $requirement->title . '" expects.')
                    . ' If you believe this is a mistake, please contact your Program Head.',
            ]);
        }

        $path = $file->store('submissions', 'public');
        $isLate = $requirement->deadline->isPast() && !$requirement->deadline->isToday();

        // A new file goes back to the Secretary's "waiting for your check".
        Submission::updateOrCreate($key, [
            'faculty_id'           => $faculty->id,
            'file_path'            => $path,
            'file_name'            => $file->getClientOriginalName(),
            'file_type'            => $extension,
            'file_size'            => $file->getSize(),
            'file_hash'            => $hash,
            'status'               => $isLate ? 'late' : 'submitted',
            'submitted_at'         => now(),
            'filing_status'        => null,
            'filing_checked_by'    => null,
            'filing_checked_at'    => null,
            'filing_return_reason' => null,
        ]);

        if ($previous && $previous->file_path !== $path) {
            Storage::disk('public')->delete($previous->file_path);
        }

        return back()->with('success', $isLate ? 'Submitted (marked as late).' : 'Submitted successfully.');
    }

    // The same file can't be submitted twice: not under the same requirement (for another
    // course, or by anyone), and not by the same person for another requirement. A file name
    // can't repeat under the same requirement either. Replacing your own file is fine.
    protected function duplicateMessage(SubmissionRequirement $requirement, int $facultyId, ?int $replacingId, string $hash, string $name): ?string
    {
        $others = Submission::with(['course', 'faculty', 'requirement'])
            ->when($replacingId, fn ($q) => $q->where('id', '!=', $replacingId));

        $sameFile = (clone $others)->where('file_hash', $hash)
            ->where(fn ($q) => $q->where('requirement_id', $requirement->id)->orWhere('faculty_id', $facultyId))
            ->first();
        if ($sameFile && $sameFile->faculty_id === $facultyId && $requirement->counterparts()->contains('id', $sameFile->requirement_id)) {
            return 'You already submitted this file for ' . $sameFile->requirement->program . ' – "' . $sameFile->requirement->title
                . '". It already counts for this requirement too, so you don\'t need to submit it again.';
        }
        if ($sameFile) {
            return 'This file was already submitted ' . $this->where($sameFile, $requirement)
                . '. Choose the correct file' . ($requirement->per_course ? ' for this course.' : '.');
        }

        $sameName = (clone $others)->where('requirement_id', $requirement->id)->where('file_name', $name)->first();
        if ($sameName) {
            return 'A file named "' . $name . '" was already submitted ' . $this->where($sameName, $requirement)
                . '. Rename it or choose the correct file.';
        }

        return null;
    }

    protected function where(Submission $existing, SubmissionRequirement $requirement): string
    {
        $parts = [];
        if ($existing->requirement_id !== $requirement->id) {
            $parts[] = 'for "' . ($existing->requirement->title ?? 'another requirement') . '"';
        }
        if ($existing->course) {
            $parts[] = 'for ' . $existing->course->code;
        }
        $by = $existing->faculty_id === auth()->id() ? 'by you' : 'by ' . ($existing->faculty->name ?? 'another faculty member');

        return trim(implode(' ', $parts) . ' ' . $by);
    }
}
