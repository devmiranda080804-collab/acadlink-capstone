<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\TemplateDocument;
use App\Services\AcademicDocumentValidator;
use App\Services\OnlyOffice;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

// The Secretary uploads new templates and submits them to the Dean for approval.
// Only the Dean sends approved templates to programs (Admin\TemplateApprovalController).
class TemplateDistributionController extends Controller
{
    public function index()
    {
        $templates = TemplateDocument::with(['creator', 'reviewer', 'programs'])
            ->whereNull('superseded_at')
            ->latest()
            ->get();

        return view('secretary.template-distribution', compact('templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type'  => ['required', Rule::in(array_keys(TemplateDocument::types()))],
            'file'  => TemplateDocument::fileRule(),
        ]);

        $file = $request->file('file');
        if ($error = $this->contentCheckError($file, $request->type)) {
            return back()->withErrors(['file' => $error])->withInput();
        }

        $document = $this->storeAsPending($file, [
            'title' => $request->title,
            'type'  => $request->type,
        ]);

        AuditLog::record('Template Submitted', "{$document->title} submitted to the Dean for approval by " . auth()->user()->name);

        return back()->with('success', 'Template submitted to the Dean for approval.');
    }

    // A new version of an approved or returned template. The current approved version
    // stays visible to faculty until the Dean approves this one.
    public function newVersion(Request $request, TemplateDocument $template)
    {
        $hasPendingVersion = TemplateDocument::where('root_template_id', $template->root_template_id)
            ->whereIn('status', [TemplateDocument::STATUS_PENDING, TemplateDocument::STATUS_DRAFT])
            ->whereNull('superseded_at')
            ->exists();
        abort_if($hasPendingVersion, 422, 'This template already has a draft or a version waiting for the Dean. Finish that one before uploading another.');

        $request->validate([
            'title' => 'nullable|string|max:255',
            'file'  => TemplateDocument::fileRule(),
        ]);

        $file = $request->file('file');
        if ($error = $this->contentCheckError($file, $template->type)) {
            return back()->withErrors(['file' => $error]);
        }

        $latestVersion = TemplateDocument::where('root_template_id', $template->root_template_id)->max('version');

        $newVersion = $this->storeAsPending($file, [
            'title'            => $request->title ?: $template->title,
            'type'             => $template->type,
            'root_template_id' => $template->root_template_id,
            'version'          => $latestVersion + 1,
        ]);

        // A returned version is replaced outright; an approved one keeps serving
        // faculty until the Dean approves the new version.
        if ($template->isReturned()) {
            $template->update(['superseded_at' => now()]);
        }

        AuditLog::record('Template Submitted', "{$newVersion->title} (v{$newVersion->version}) submitted to the Dean for approval by " . auth()->user()->name);

        return back()->with('success', "Version {$newVersion->version} submitted to the Dean for approval.");
    }

    // Starts a new draft version from an approved or returned template, or reopens the draft
    // already in progress. What faculty use doesn't change until the Dean approves it.
    public function startEditing(TemplateDocument $template)
    {
        $open = TemplateDocument::where('root_template_id', $template->root_template_id)
            ->whereIn('status', [TemplateDocument::STATUS_DRAFT, TemplateDocument::STATUS_PENDING])
            ->whereNull('superseded_at')
            ->first();

        if ($open?->isPending()) {
            return back()->withErrors(['template' => 'A version of this template is waiting for the Dean. Wait for a decision before editing it.']);
        }
        if ($open) {
            return redirect('/secretary/template-distribution/' . $open->id . '/editor');
        }
        if (!$template->isEditableFile()) {
            return back()->withErrors(['template' => 'Only Word, Excel and PowerPoint templates can be edited in the system.']);
        }

        $path = 'template-documents/' . bin2hex(random_bytes(20)) . '.' . $template->extension();
        Storage::disk('public')->copy($template->file_path, $path);

        $draft = TemplateDocument::create([
            'created_by'       => auth()->id(),
            'title'            => $template->title,
            'type'             => $template->type,
            'root_template_id' => $template->root_template_id,
            'version'          => TemplateDocument::where('root_template_id', $template->root_template_id)->max('version') + 1,
            'status'           => TemplateDocument::STATUS_DRAFT,
            'file_path'        => $path,
            'file_name'        => $template->file_name,
            'file_type'        => $template->file_type,
            'file_size'        => $template->file_size,
        ]);

        return redirect('/secretary/template-distribution/' . $draft->id . '/editor');
    }

    public function editor(TemplateDocument $template)
    {
        abort_unless($template->isDraft(), 422, 'Only drafts can be edited. Start a new draft from this template instead.');

        if (!OnlyOffice::isConfigured()) {
            return redirect('/secretary/template-distribution')->withErrors(['template' => 'The document editor is not available right now.']);
        }

        return view('documents.editor', [
            'title'     => $template->title . ' (draft v' . $template->version . ')',
            'backUrl'   => url('/secretary/template-distribution'),
            'backLabel' => 'Back to Templates',
            'canEdit'   => $template->canEdit(auth()->user()),
            'config'    => (new OnlyOffice())->editorConfig($template, auth()->user()),
            'scriptUrl' => OnlyOffice::scriptUrl(),
        ]);
    }

    public function submitDraft(TemplateDocument $template)
    {
        abort_unless($template->isDraft(), 422, 'This draft has already been submitted.');

        $template->update(['status' => TemplateDocument::STATUS_PENDING]);

        // A returned version is replaced outright; an approved one keeps serving faculty.
        TemplateDocument::where('root_template_id', $template->root_template_id)
            ->where('status', TemplateDocument::STATUS_RETURNED)
            ->whereNull('superseded_at')
            ->update(['superseded_at' => now()]);

        AuditLog::record('Template Submitted', "{$template->title} (v{$template->version}) edited in the system and submitted to the Dean by " . auth()->user()->name);

        return back()->with('success', "Version {$template->version} submitted to the Dean for approval.");
    }

    // The Secretary can withdraw their own submission while it's pending or returned.
    public function destroy(TemplateDocument $template)
    {
        abort_if($template->isApproved(), 403, 'Approved templates can only be removed by the Dean.');

        Storage::disk('public')->delete($template->file_path);
        $title = $template->title;
        $template->delete();

        AuditLog::record('Template Withdrawn', "{$title} withdrawn by " . auth()->user()->name);

        return back()->with('success', 'Template withdrawn.');
    }

    protected function contentCheckError(UploadedFile $file, string $type): ?string
    {
        $check = (new AcademicDocumentValidator())->validate($file->getRealPath(), $file->getClientOriginalExtension(), $type);

        if ($check['valid']) {
            return null;
        }

        return ($check['reason'] ?? 'This file does not appear to match the selected template type.')
            . ' Please upload the correct type of document, or pick a different Template Type.';
    }

    protected function storeAsPending(UploadedFile $file, array $attributes): TemplateDocument
    {
        return TemplateDocument::create(array_merge($attributes, [
            'created_by' => auth()->id(),
            'status'     => TemplateDocument::STATUS_PENDING,
            'file_path'  => $file->store('template-documents', 'public'),
            'file_name'  => $file->getClientOriginalName(),
            'file_type'  => $file->getClientOriginalExtension(),
            'file_size'  => $file->getSize(),
        ]));
    }
}
