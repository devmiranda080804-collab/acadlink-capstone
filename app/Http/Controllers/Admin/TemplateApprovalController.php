<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;

class TemplateApprovalController extends Controller
{
    public function index()
    {
        // Admin nakakakita ng lahat ng program — kasama history
        $templates = Template::with(['faculty', 'reviewer'])
            ->latest()
            ->get();

        return view('admin.system-approvals', compact('templates'));
    }

    public function approve(Request $request, Template $template)
    {
        // Admin pwedeng mag-approve lang ng na-forward na ni PH
        abort_unless($template->status === 'pending_approval', 403);

        $template->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'review_note' => null,
        ]);

        return back()->with('success', 'Template approved. Available na ito para sa distribution.');
    }

    public function reject(Request $request, Template $template)
    {
        abort_unless($template->status === 'pending_approval', 403);

        $request->validate([
            'review_note' => 'required|string|max:1000',
        ]);

        $template->update([
            'status'      => 'rejected',
            'approved_by' => auth()->id(),
            'review_note' => $request->review_note,
        ]);

        return back()->with('success', 'Template rejected and returned to faculty.');
    }
}