<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\TemplateDocument;
use App\Services\AcademicDocumentValidator;
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
            'type'  => ['required', Rule::in(array_keys(TemplateDocument::TYPES))],
            'file'  => 'required|file|mimes:pdf,doc,docx|max:20480',
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
            ->where('status', TemplateDocument::STATUS_PENDING)
            ->whereNull('superseded_at')
            ->exists();
        abort_if($hasPendingVersion, 422, 'A version of this template is still waiting for the Dean. Wait for a decision before uploading another.');

        $request->validate([
            'title' => 'nullable|string|max:255',
            'file'  => 'required|file|mimes:pdf,doc,docx|max:20480',
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
