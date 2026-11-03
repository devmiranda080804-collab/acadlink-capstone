<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\ContentModule;
use App\Models\TemplateDocument;
use App\Models\TemplateDocumentView;
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

        return view('faculty.my-template', compact('templates'));
    }

    // A personal copy in the faculty member's CMS. The official template stays as it is.
    public function useTemplate(TemplateDocument $template)
    {
        abort_unless($template->isApproved() && $template->superseded_at === null && $template->isDistributedTo((string) auth()->user()->program), 403);
        abort_unless($template->isEditableFile(), 422, 'Only Word, Excel and PowerPoint templates can be copied for editing.');

        $path = 'content-modules/' . bin2hex(random_bytes(20)) . '.' . $template->extension();
        Storage::disk('public')->copy($template->file_path, $path);

        $module = ContentModule::create([
            'created_by'  => auth()->id(),
            'title'       => $template->title,
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
