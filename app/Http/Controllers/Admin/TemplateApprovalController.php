<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateDocument;
use App\Services\AcademicDocumentValidator;
use App\Services\GoogleDocsService;
use App\Support\Programs;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class TemplateApprovalController extends Controller
{
    public function index()
    {
        $templates = TemplateDocument::with(['creator', 'forwarder', 'programs'])
            ->latest()
            ->get();

        return view('admin.system-approvals', compact('templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'type'        => 'required|string|max:100',
            'file'        => 'required|file|mimes:pdf,doc,docx|max:20480',
            'programs'    => 'required|array|min:1',
            'programs.*'  => 'in:' . implode(',', Programs::codes()),
        ]);

        // Templates are now always a plain file through the whole pipeline —
        // Secretary forwards it, Program Head distributes it, and faculty
        // view/download it. No Google Doc is created here anymore (legacy
        // google_doc_id-based templates, if any remain, still work via the
        // backward-compatible branches in destroy() and elsewhere).
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

        $path = $file->store('template-documents', 'public');

        $data = [
            'created_by' => auth()->id(),
            'title'      => $request->title,
            'type'       => $request->type,
            'file_path'  => $path,
            'file_name'  => $file->getClientOriginalName(),
            'file_type'  => $file->getClientOriginalExtension(),
            'file_size'  => $file->getSize(),
        ];

        $document = TemplateDocument::create($data);

        // Only the Dean/Admin-selected programs get a row — each Program
        // Head only ever sees templates meant for their own program (see
        // ProgramHead\TemplateReviewController::index()'s whereHas filter).
        foreach ($request->programs as $program) {
            $document->programs()->create(['program' => $program]);
        }

        AuditLog::record('Template Provided', "{$document->title} uploaded by " . auth()->user()->name . ' for Secretary to relay.');

        return back()->with('success', 'Template uploaded. It now awaits the Secretary to forward it to the Program Heads.');
    }

    public function destroy(TemplateDocument $template)
    {
        abort_if($template->isForwarded(), 403, 'This template has already been forwarded and can no longer be removed here.');

        if ($template->file_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($template->file_path);
        }

        if ($template->google_doc_id) {
            (new GoogleDocsService())->deleteDocument($template->google_doc_id);
        }

        $template->delete();

        return back()->with('success', 'Template removed.');
    }
}
