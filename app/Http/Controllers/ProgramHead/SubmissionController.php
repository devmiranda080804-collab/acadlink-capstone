<?php

namespace App\Http\Controllers\ProgramHead;

use App\Http\Controllers\Controller;
use App\Models\SubmissionRequirement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SubmissionController extends Controller
{
    public function index()
    {
        $myProgram = auth()->user()->program;

        $requirements = SubmissionRequirement::with(['submissions.faculty', 'submissions.course'])
            ->where('program', $myProgram)
            ->orderByPriority()
            ->get();

        // Count of faculty in the program (for "X of Y submitted")
        $facultyCount = User::where('role', 'faculty')
            ->inProgram($myProgram)
            ->whereNull('archived_at')
            ->count();

        // A per-course requirement expects one file for each course taught in its term.
        // Shared faculty are counted once for the group: a file they sent for the same kind of
        // requirement in another of their programs counts here too (panel comment #27).
        foreach ($requirements as $req) {
            $covered = $req->per_course ? collect() : $req->coveringSubmissions();
            $req->setAttribute('covered', $covered);
            $req->setAttribute('expected', $req->per_course ? $req->courses()->count() : $facultyCount);
            $req->setAttribute('submitted_count', $req->per_course
                ? $req->submissions->whereNotNull('course_id')->unique('course_id')->count()
                : $req->submissions->unique('faculty_id')->count() + $covered->count());
        }

        return view('program-head.submissions', compact('requirements', 'facultyCount', 'myProgram'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type'        => ['required', Rule::in(array_keys(SubmissionRequirement::typeOptions()))],
            'priority'    => ['required', Rule::in(array_keys(SubmissionRequirement::PRIORITIES))],
            'file_types'   => 'nullable|array',
            'file_types.*' => [Rule::in(array_keys(SubmissionRequirement::FILE_TYPES))],
            'deadline'    => 'required|date|after_or_equal:today',
        ], [
            'deadline.after_or_equal' => 'The deadline can\'t be earlier than today.',
        ]);

        SubmissionRequirement::create([
            'created_by'  => auth()->id(),
            'program'     => auth()->user()->program,
            'title'       => $request->title,
            'description' => $request->description,
            'type'        => $request->type,
            'file_types'  => array_values(array_unique($request->file_types ?: SubmissionRequirement::DEFAULT_FILE_TYPES)),
            'per_course'  => $request->boolean('per_course'),
            'school_year' => \App\Support\AcademicTerm::currentSchoolYear(),
            'semester'    => \App\Support\AcademicTerm::currentSemester(),
            'priority'    => $request->priority,
            'deadline'    => $request->deadline,
        ]);

        return back()->with('success', 'Submission requirement created.');
    }

    public function update(Request $request, SubmissionRequirement $requirement)
    {
        abort_unless($requirement->program === auth()->user()->program, 403);

        // A deadline that's already passed can stay as-is when only the title or
        // description changes; it just can't be moved to a past date.
        $deadlineChanged = $request->input('deadline') !== $requirement->deadline->format('Y-m-d');

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type'        => ['required', Rule::in([...array_keys(SubmissionRequirement::typeOptions()), $requirement->type])],
            'priority'    => ['required', Rule::in(array_keys(SubmissionRequirement::PRIORITIES))],
            'file_types'   => 'nullable|array',
            'file_types.*' => [Rule::in(array_keys(SubmissionRequirement::FILE_TYPES))],
            'deadline'    => $deadlineChanged ? 'required|date|after_or_equal:today' : 'required|date',
        ], [
            'deadline.after_or_equal' => 'The deadline can\'t be earlier than today.',
        ]);

        $requirement->update($request->only(['title', 'description', 'type', 'priority', 'deadline'])
            + ['file_types' => array_values(array_unique($request->file_types ?: SubmissionRequirement::DEFAULT_FILE_TYPES))]);
        // Who submits what can only change before anything is submitted.
        if (!$requirement->submissions()->exists()) {
            $requirement->update(['per_course' => $request->boolean('per_course')]);
        }

        return back()->with('success', 'Requirement updated.');
    }

    public function destroy(SubmissionRequirement $requirement)
    {
        abort_unless($requirement->program === auth()->user()->program, 403);

        // Deleting the requirement cascades to its Submission rows in the DB,
        // but the actual uploaded files on disk are only cleaned up here.
        foreach ($requirement->submissions as $submission) {
            Storage::disk('public')->delete($submission->file_path);
        }

        $requirement->delete();

        return back()->with('success', 'Requirement deleted.');
    }
}