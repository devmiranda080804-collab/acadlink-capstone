<?php

namespace App\Http\Controllers\ProgramHead;

use App\Http\Controllers\Controller;
use App\Models\TemplateDocument;
use App\Models\User;
use App\Services\GoogleDocsService;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class TemplateReviewController extends Controller
{
    public function index()
    {
        $myProgram = auth()->user()->program;

        // Only templates the Secretary has already relayed, and that target my program
        $templates = TemplateDocument::with(['creator', 'programs'])
            ->whereNotNull('forwarded_at')
            ->whereHas('programs', fn($p) => $p->where('program', $myProgram))
            ->latest()
            ->get();

        return view('program-head.template-review', compact('templates', 'myProgram'));
    }

    public function distribute(Request $request, TemplateDocument $template)
    {
        $myProgram = auth()->user()->program;

        abort_unless($template->isForwarded(), 403, 'This template has not been forwarded by the Secretary yet.');

        $row = $template->programRow($myProgram);
        abort_unless($row, 403);
        abort_if($row->distributed_at, 403, 'Already distributed to your faculty.');

        $row->update([
            'distributed_by' => auth()->id(),
            'distributed_at' => now(),
        ]);

        // Google Doc templates: give every faculty in the program view-only
        // access to the protected master, so they can preview it and make
        // their own editable copy
        if ($template->google_doc_id) {
            $google = new GoogleDocsService();
            $facultyEmails = User::where('role', 'faculty')
                ->where('program', $myProgram)
                ->whereNotNull('google_email')
                ->pluck('google_email');

            foreach ($facultyEmails as $email) {
                $google->shareWithEmail($template->google_doc_id, $email, 'reader');
            }
        }

        AuditLog::record('Template Distributed', "{$template->title} distributed to {$myProgram} faculty by " . auth()->user()->name);

        return back()->with('success', 'Template distributed to all faculty in your program.');
    }
}
