<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateDocument;
use App\Services\AcademicDocumentValidator;
use App\Support\Programs;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\AuditLog;

class TemplateApprovalController extends Controller
{
    public function index()
    {
        // Only the current version of each template — superseded ones stay
        // in the database (real version history) but drop out of the normal
        // working list. See versions() for the full history of a template.
        $templates = TemplateDocument::with(['creator', 'forwarder', 'programs'])
            ->whereNull('superseded_at')
            ->latest()
            ->get();

        return view('admin.system-approvals', compact('templates'));
    }

    // Full version history for one template's lineage — read-only, for the
    // "Version History" view on its card.
    public function versions(TemplateDocument $template)
    {
        $versions = $template->allVersions()->with(['creator', 'forwarder'])->get();

        return response()->json($versions->map(fn($v) => [
            'id'            => $v->id,
            'version'       => $v->version,
            'created_by'    => $v->creator->name ?? 'Unknown',
            'created_at'    => $v->created_at->format('Y-m-d h:i A'),
            'file_url'      => \Illuminate\Support\Facades\Storage::url($v->file_path),
            'is_current'    => $v->superseded_at === null,
            'is_forwarded'  => $v->isForwarded(),
        ]));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'type'        => ['required', Rule::in(array_keys(TemplateDocument::TYPES))],
            'file'        => 'required|file|mimes:pdf,doc,docx|max:20480',
            'programs'    => 'required|array|min:1',
            'programs.*'  => 'in:' . implode(',', Programs::codes()),
        ]);

        // Templates are always a plain file through the whole pipeline —
        // Secretary forwards it, Program Head distributes it, and faculty
        // view/download it. No Google Docs anywhere in this pipeline.
        $file = $request->file('file');

        // Content sanity-check — this is specifically claimed to be a
        // {type}, so it should actually look like one.
        $check = (new AcademicDocumentValidator())->validate($file->getRealPath(), $file->getClientOriginalExtension(), $request->type);
        if (!$check['valid']) {
            return back()->withErrors([
                'file' => ($check['reason'] ?? 'This file does not appear to match the selected template type.')
                    . ' Please upload the correct type of document, or pick a different Template Type.',
            ]);
        }

        $document = $this->storeFileAsDocument($file, [
            'created_by' => auth()->id(),
            'title'      => $request->title,
            'type'       => $request->type,
        ], $request->programs);

        AuditLog::record('Template Provided', "{$document->title} uploaded by " . auth()->user()->name . ' for Secretary to relay.');

        return back()->with('success', 'Template uploaded. It now awaits the Secretary to forward it to the Program Heads.');
    }

    // Replaces an existing template with an updated file — the old row isn't
    // deleted, it's marked superseded and kept as version history (see
    // TemplateDocument::allVersions()). The new version is a fresh row that
    // starts its own Admin -> Secretary -> Program Head -> Faculty pipeline
    // cycle from scratch (Awaiting Secretary again) rather than silently
    // inheriting the old version's forwarded/distributed state, since the
    // content genuinely changed and should be reviewed again at each step.
    public function newVersion(Request $request, TemplateDocument $template)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'file'  => 'required|file|mimes:pdf,doc,docx|max:20480',
        ]);

        $file = $request->file('file');

        $check = (new AcademicDocumentValidator())->validate($file->getRealPath(), $file->getClientOriginalExtension(), $template->type);
        if (!$check['valid']) {
            return back()->withErrors([
                'file' => ($check['reason'] ?? 'This file does not appear to match this template\'s type.')
                    . ' Please upload the correct type of document.',
            ]);
        }

        $newVersion = $this->storeFileAsDocument($file, [
            'created_by'       => auth()->id(),
            'title'            => $request->title ?: $template->title,
            'type'             => $template->type,
            'root_template_id' => $template->root_template_id,
            'version'          => $template->version + 1,
        ], $template->programs->pluck('program')->all());

        $template->update(['superseded_at' => now()]);

        AuditLog::record('Template Provided', "{$newVersion->title} (v{$newVersion->version}) uploaded by " . auth()->user()->name . ' for Secretary to relay, replacing v' . $template->version . '.');

        return back()->with('success', "New version (v{$newVersion->version}) uploaded. It now awaits the Secretary to forward it to the Program Heads.");
    }

    protected function storeFileAsDocument(\Illuminate\Http\UploadedFile $file, array $attributes, array $programs): TemplateDocument
    {
        $path = $file->store('template-documents', 'public');

        $document = TemplateDocument::create(array_merge($attributes, [
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
        ]));

        // Only the Dean/Admin-selected programs get a row — each Program
        // Head only ever sees templates meant for their own program (see
        // ProgramHead\TemplateReviewController::index()'s whereHas filter).
        foreach ($programs as $program) {
            $document->programs()->create(['program' => $program]);
        }

        return $document;
    }

    public function destroy(TemplateDocument $template)
    {
        abort_if($template->isForwarded(), 403, 'This template has already been forwarded and can no longer be removed here.');

        if ($template->file_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($template->file_path);
        }

        $template->delete();

        return back()->with('success', 'Template removed.');
    }
}
