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

        // Program requirements, sorted by nearest deadline
        $requirements = SubmissionRequirement::with('submissions.faculty')
            ->where('program', $myProgram)
            ->orderBy('deadline')
            ->get();

        // Count of faculty in the program (for "X of Y submitted")
        $facultyCount = User::where('role', 'faculty')
            ->where('program', $myProgram)
            ->whereNull('archived_at')
            ->count();

        return view('program-head.submissions', compact('requirements', 'facultyCount', 'myProgram'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type'        => ['required', Rule::in(array_keys(SubmissionRequirement::typeOptions()))],
            'deadline'    => 'required|date',
        ]);

        SubmissionRequirement::create([
            'created_by'  => auth()->id(),
            'program'     => auth()->user()->program,
            'title'       => $request->title,
            'description' => $request->description,
            'type'        => $request->type,
            'deadline'    => $request->deadline,
        ]);

        return back()->with('success', 'Submission requirement created.');
    }

    public function update(Request $request, SubmissionRequirement $requirement)
    {
        abort_unless($requirement->program === auth()->user()->program, 403);

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type'        => ['required', Rule::in(array_keys(SubmissionRequirement::typeOptions()))],
            'deadline'    => 'required|date',
        ]);

        $requirement->update($request->only(['title', 'description', 'type', 'deadline']));

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