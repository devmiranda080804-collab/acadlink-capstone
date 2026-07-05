<?php

namespace App\Http\Controllers\ProgramHead;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;

class TemplateReviewController extends Controller
{
    public function index()
    {
        $myProgram = auth()->user()->program;

        // Lahat ng template sa sariling program — kasama na yung na-review na para may history
        $templates = Template::with('faculty')
            ->where('program', $myProgram)
            ->latest()
            ->get();

        return view('program-head.template-review', compact('templates', 'myProgram'));
    }

    public function approve(Request $request, Template $template)
    {
        $this->authorizeTemplate($template);

        // PH-level approval → papunta na kay Admin
        $template->update([
            'status'      => 'pending_approval',
            'reviewed_by' => auth()->id(),
            'review_note' => null,
        ]);

        return back()->with('success', 'Template approved and forwarded to Admin/Dean for final approval.');
    }

    public function needsRevision(Request $request, Template $template)
    {
        $this->authorizeTemplate($template);

        $request->validate([
            'review_note' => 'required|string|max:1000',
        ]);

        $template->update([
            'status'      => 'needs_revision',
            'reviewed_by' => auth()->id(),
            'review_note' => $request->review_note,
        ]);

        return back()->with('success', 'Template returned to faculty for revision.');
    }

    // Security: PH pwedeng mag-review lang ng template sa sariling program
    protected function authorizeTemplate(Template $template): void
    {
        abort_unless($template->program === auth()->user()->program, 403);
    }
}