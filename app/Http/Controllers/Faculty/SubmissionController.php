<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\SubmissionRequirement;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
        public function index()
    {
        $myProgram = auth()->user()->program;
        $facultyId = auth()->id();

        $requirements = SubmissionRequirement::where('program', $myProgram)
            ->orderBy('deadline')
            ->get()
            ->map(function ($req) use ($facultyId) {
                $mySubmission = Submission::where('requirement_id', $req->id)
                    ->where('faculty_id', $facultyId)
                    ->first();

                return [
                    'id'          => $req->id,
                    'title'       => $req->title,
                    'description' => $req->description,
                    'type'        => ucwords(str_replace('_', ' ', $req->type)),
                    'deadline'    => $req->deadline,
                    'days_left'   => $req->days_left,
                    'submission'  => $mySubmission,
                ];
            });

        $urgentCount = $requirements->filter(function ($req) {
            return !$req['submission'] && $req['days_left'] <= 3;
        })->count();

        return view('faculty.submissions', compact('requirements', 'urgentCount'));
    }

    public function store(Request $request, SubmissionRequirement $requirement)
    {
        abort_unless($requirement->program === auth()->user()->program, 403);

        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:20480',
        ]);

        $file = $request->file('file');
        $path = $file->store('submissions', 'public');

        $isLate = $requirement->deadline->isPast() && !$requirement->deadline->isToday();

        // Update or create — kasi isa lang na pasa per requirement per faculty
        Submission::updateOrCreate(
            ['requirement_id' => $requirement->id, 'faculty_id' => auth()->id()],
            [
                'file_path'    => $path,
                'file_name'    => $file->getClientOriginalName(),
                'file_type'    => $file->getClientOriginalExtension(),
                'file_size'    => $file->getSize(),
                'status'       => $isLate ? 'late' : 'submitted',
                'submitted_at' => now(),
            ]
        );

        return back()->with('success', $isLate ? 'Submitted (marked as late).' : 'Submitted successfully.');
    }
}