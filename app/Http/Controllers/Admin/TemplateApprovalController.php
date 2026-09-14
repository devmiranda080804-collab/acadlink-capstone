<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateDocument;
use App\Services\GoogleDocsService;
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
            'title' => 'required|string|max:255',
            'type'  => 'required|string|max:100',
            'mode'  => 'required|in:google_doc,upload_file',
            'file'  => 'required_if:mode,upload_file|nullable|file|mimes:pdf,doc,docx|max:20480',
        ]);

        $data = [
            'created_by' => auth()->id(),
            'title'      => $request->title,
            'type'       => $request->type,
        ];

        if ($request->mode === 'google_doc') {
            // Create it directly as an editable Google Doc — this becomes the
            // protected "master" faculty can view and copy from
            $data['google_doc_id'] = (new GoogleDocsService())->createDocument($request->title);
        } else {
            $file = $request->file('file');
            $path = $file->store('template-documents', 'public');

            $data['file_path'] = $path;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = $file->getClientOriginalExtension();
            $data['file_size'] = $file->getSize();
        }

        $document = TemplateDocument::create($data);

        // Every program gets its own row so each Program Head can distribute independently
        foreach (['BSA', 'BSMA', 'BSOA'] as $program) {
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
