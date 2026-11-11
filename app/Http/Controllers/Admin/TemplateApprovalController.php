<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\TemplateDocument;
use App\Services\DocxComments;
use App\Services\OnlyOffice;
use App\Support\FileLinks;
use App\Support\Programs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// The Dean reviews templates the Secretary submitted: approve and send them to
// programs (faculty there can then see them), or return them with a reason.
class TemplateApprovalController extends Controller
{
    public function index()
    {
        // Only the current version of each template; older versions are in versions().
        $templates = TemplateDocument::with(['creator', 'reviewer', 'programs'])
            ->submitted()
            ->whereNull('superseded_at')
            ->latest()
            ->get();

        return view('admin.system-approvals', compact('templates'));
    }

    // The file on the left (with comments for a pending Word, Excel or PowerPoint file), and
    // the decision on the right: approve and send, return with a reason, or send to more programs.
    public function review(TemplateDocument $template)
    {
        abort_if($template->isDraft(), 404);

        $template->load(['creator', 'reviewer', 'programs']);
        $user = auth()->user();

        // Programs the approved version of this template went to, suggested when approving.
        $suggested = $template->sentPrograms() ?: (TemplateDocument::with('programs')
            ->where('root_template_id', $template->root_template_id)
            ->where('id', '!=', $template->id)
            ->where('status', TemplateDocument::STATUS_APPROVED)
            ->latest('id')
            ->first()?->sentPrograms() ?? []);

        return view('admin.template-review', [
            'template'          => $template,
            'sentPrograms'      => $template->sentPrograms(),
            'suggestedPrograms' => $suggested,
            'canComment'        => OnlyOffice::isConfigured() && $template->canComment($user),
            'config'            => OnlyOffice::isConfigured() && $template->file_path ? (new OnlyOffice())->editorConfig($template, $user) : null,
            'scriptUrl'         => OnlyOffice::scriptUrl(),
        ]);
    }

    // Before a decision: ask an open editor to save the comments; status() says when they arrived.
    public function prepare(TemplateDocument $template, OnlyOffice $onlyOffice)
    {
        $stamp = optional($template->last_edited_at)->toIso8601String();

        return response()->json(['saving' => $template->isPending() && $onlyOffice->forceSave($template), 'stamp' => $stamp]);
    }

    public function saveStatus(TemplateDocument $template)
    {
        return response()->json(['stamp' => optional($template->fresh()->last_edited_at)->toIso8601String()]);
    }

    public function versions(TemplateDocument $template)
    {
        $versions = $template->allVersions()->with(['creator', 'reviewer'])->get();

        return response()->json($versions->map(fn($v) => [
            'id'          => $v->id,
            'version'     => $v->version,
            'created_by'  => $v->creator->name ?? 'Unknown',
            'created_at'  => $v->created_at->format('Y-m-d h:i A'),
            'view_url'     => FileLinks::view('template', $v->id),
            'download_url' => FileLinks::download('template', $v->id),
            'is_current'  => $v->superseded_at === null,
            'status'      => $v->status,
        ]));
    }

    public function approve(Request $request, TemplateDocument $template)
    {
        abort_unless($template->isPending(), 422, 'Only templates waiting for approval can be approved.');
        $programs = $this->validatedPrograms($request);

        $template->update([
            'status'        => TemplateDocument::STATUS_APPROVED,
            'reviewed_by'   => auth()->id(),
            'reviewed_at'   => now(),
            'return_reason' => null,
        ]);

        // Faculty get the template without the review comments.
        $this->removeComments($template);

        // A newly approved version replaces the one faculty were using.
        TemplateDocument::where('root_template_id', $template->root_template_id)
            ->where('id', '!=', $template->id)
            ->whereNull('superseded_at')
            ->update(['superseded_at' => now()]);

        $this->sendTo($template, $programs);

        AuditLog::record('Template Approved', "{$template->title} (v{$template->version}) approved by " . auth()->user()->name . ' and sent to ' . implode(', ', $programs));

        return redirect('/admin/template-approvals')->with('success', 'Template approved and sent to: ' . implode(', ', $programs) . '.');
    }

    // Send an already approved template to more programs.
    public function send(Request $request, TemplateDocument $template)
    {
        abort_unless($template->isApproved(), 422, 'Approve this template before sending it to programs.');
        $programs = $this->validatedPrograms($request);

        $this->sendTo($template, $programs);

        AuditLog::record('Template Sent', "{$template->title} sent to " . implode(', ', $programs) . ' by ' . auth()->user()->name);

        return redirect('/admin/template-approvals')->with('success', 'Template sent to: ' . implode(', ', $programs) . '.');
    }

    public function returnToSecretary(Request $request, TemplateDocument $template)
    {
        abort_unless($template->isPending(), 422, 'Only templates waiting for approval can be returned.');

        $request->validate(['reason' => 'required|string|max:1000']);

        $template->update([
            'status'        => TemplateDocument::STATUS_RETURNED,
            'reviewed_by'   => auth()->id(),
            'reviewed_at'   => now(),
            'return_reason' => $request->reason,
        ]);

        AuditLog::record('Template Returned', "{$template->title} returned to the Secretary by " . auth()->user()->name . ": {$request->reason}");

        return redirect('/admin/template-approvals')->with('success', 'Template returned to the Secretary.');
    }

    // Removing a sent template also takes it out of the Secretary's repository and
    // the faculty libraries — those read from this row, and its program/view rows cascade.
    public function destroy(TemplateDocument $template)
    {
        $sentTo = implode(', ', $template->sentPrograms());

        Storage::disk('public')->delete($template->file_path);

        $title = $template->title;
        $template->delete();

        AuditLog::record(
            'Template Removed',
            "{$title} removed by " . auth()->user()->name . ($sentTo ? " (was sent to: {$sentTo})" : '')
        );

        return back()->with('success', 'Template removed.');
    }

    protected function removeComments(TemplateDocument $template): void
    {
        if ($template->extension() !== 'docx' || !$template->file_path || !Storage::disk('public')->exists($template->file_path)) {
            return;
        }

        $path = Storage::disk('public')->path($template->file_path);
        if (DocxComments::strip($path)) {
            clearstatcache(true, $path);
            $template->forceFill([
                'file_size'    => filesize($path),
                // The editor must not reopen its cached copy with the comments.
                'document_key' => OnlyOffice::newKey($template),
            ])->save();
        }
    }

    protected function validatedPrograms(Request $request): array
    {
        $request->validate([
            'programs'   => 'required|array|min:1',
            'programs.*' => 'in:' . implode(',', Programs::codes()),
        ], [
            'programs.required' => 'Choose at least one program to send this template to.',
        ]);

        return array_values(array_unique($request->programs));
    }

    protected function sendTo(TemplateDocument $template, array $programs): void
    {
        foreach ($programs as $program) {
            $row = $template->programs()->firstOrNew(['program' => $program]);
            if (!$row->distributed_at) {
                $row->fill(['distributed_by' => auth()->id(), 'distributed_at' => now()])->save();
            }
        }
    }
}
