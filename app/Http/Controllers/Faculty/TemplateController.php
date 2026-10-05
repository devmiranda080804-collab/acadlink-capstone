<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\TemplateDocument;
use App\Models\TemplateDocumentView;

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
