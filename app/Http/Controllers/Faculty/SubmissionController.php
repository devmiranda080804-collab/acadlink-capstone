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
                    'is_due_soon' => $req->is_due_soon,
                    'submission'  => $mySubmission,
                ];
            });

        return view('faculty.submissions', compact('requirements'));
    }

    public function store(Request $request, SubmissionRequirement $requirement)
    {
        abort_unless($requirement->program === auth()->user()->program, 403);

        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:20480',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

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

        // Update or create — only one submission per requirement per faculty
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