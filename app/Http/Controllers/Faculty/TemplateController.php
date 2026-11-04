<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\ContentModule;
use App\Models\Course;
use App\Models\TemplateDocument;
use App\Models\TemplateDocumentView;
use App\Services\OnlyOffice;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{
    // Faculty just views/downloads what their Program Head has distributed —
    // no uploading, no Google Docs anywhere in this pipeline
    public function index()
    {
        $myProgram = auth()->user()->program;

        $templates = TemplateDocument::with('creator')
            ->whereNull('superseded_at')
            ->whereHas('programs', fn($p) => $p->where('program', $myProgram)->whereNotNull('distributed_at'))
            ->latest()
            ->get();

        // Which of these this faculty member hasn't opened yet — drives the
        // "NEW" badge on each card, on each folder, and the sidebar nav count.
        $viewedIds = TemplateDocumentView::where('user_id', auth()->id())
            ->whereIn('template_document_id', $templates->pluck('id'))
            ->pluck('template_document_id')
            ->all();

        // A foreach, not ->each(fn): an arrow fn returns the assigned value, and
        // each() stops at the first false — every card after the first viewed
        // one lost its NEW label.
        foreach ($templates as $t) {
            $t->is_new = !in_array($t->id, $viewedIds);
        }

        // The faculty member's own documents, newest work first: where they come back to a copy.
        $copies = ContentModule::with('libraryEntry')
            ->where('created_by', auth()->id())
            ->whereNotNull('file_path')
            ->orderByRaw('COALESCE(last_edited_at, created_at) DESC')
            ->get();

        $libraryCourses = Course::where('program', $myProgram)->orderBy('code')->get();

        return view('faculty.my-template', compact('templates', 'copies', 'libraryCourses'));
    }

    // Makes a new personal copy each time and opens it in the editor. The official template
    // stays as it is.
    public function useTemplate(TemplateDocument $template)
    {
        abort_unless($template->isApproved() && $template->superseded_at === null && $template->isDistributedTo((string) auth()->user()->program), 403);
        abort_unless($template->isEditableFile(), 422, 'Only Word, Excel and PowerPoint templates can be copied for editing.');

        if (!OnlyOffice::isConfigured()) {
            return back()->withErrors(['editor' => 'The document editor is not available right now. You can still view and download this template.']);
        }

        // Numbered so several copies of one template can be told apart.
        $earlier = ContentModule::where('created_by', auth()->id())->where('source_template_id', $template->id)->count();

        $path = 'content-modules/' . bin2hex(random_bytes(20)) . '.' . $template->extension();
        Storage::disk('public')->copy($template->file_path, $path);

        $module = ContentModule::create([
            'created_by'  => auth()->id(),
            'source_template_id' => $template->id,
            'title'       => $template->title . ($earlier ? ' (Copy ' . ($earlier + 1) . ')' : ''),
            'description' => 'Copied from the official template (v' . $template->version . ').',
            'file_path'   => $path,
            'file_name'   => $template->file_name,
            'file_type'   => $template->file_type,
            'file_size'   => $template->file_size,
        ]);

        return redirect('/faculty/cms/' . $module->id . '/edit');
    }

    // Fired (fire-and-forget) when a faculty member clicks View/Download on a
    // template — records that they've now seen it, so it drops out of "NEW"
    // without requiring them to do anything beyond what they were already doing.
    public function markViewed(TemplateDocument $template)
    {
        TemplateDocumentView::updateOrCreate(
            ['template_document_id' => $template->id, 'user_id' => auth()->id()],
            ['viewed_at' => now()]
        );

        return response()->json(['status' => 'ok']);
    }
}
