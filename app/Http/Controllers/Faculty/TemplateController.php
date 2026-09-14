<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\TemplateDocument;
use App\Services\GoogleDocsService;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    // Faculty just views what their Program Head has distributed — no more uploading
    public function index()
    {
        $myProgram = auth()->user()->program;

        $templates = TemplateDocument::with(['creator', 'copies' => function ($q) {
                $q->where('faculty_id', auth()->id());
            }])
            ->whereHas('programs', fn($p) => $p->where('program', $myProgram)->whereNotNull('distributed_at'))
            ->latest()
            ->get();

        return view('faculty.my-template', compact('templates'));
    }

    // Duplicates the protected master template into a fully editable copy
    // owned by this faculty member
    public function makeCopy(Request $request, TemplateDocument $template)
    {
        $myProgram = auth()->user()->program;
        abort_unless(
            $template->programs()->where('program', $myProgram)->whereNotNull('distributed_at')->exists(),
            403
        );
        abort_unless($template->google_doc_id, 422, 'This template has no editable Google Doc version.');

        $me = auth()->user();
        abort_unless($me->google_email, 422, 'Add your Google email to your account first — ask your Program Head, Secretary, or Admin to set it.');

        $request->validate(['title' => 'nullable|string|max:255']);
        $title = $request->title ?: ($template->title . ' — ' . $me->name);

        $google = new GoogleDocsService();
        $copyId = $google->copyDocument($template->google_doc_id, $title);
        $google->shareWithEmail($copyId, $me->google_email, 'writer');

        $copy = $template->copies()->create([
            'faculty_id'    => $me->id,
            'title'         => $title,
            'google_doc_id' => $copyId,
        ]);

        return response()->json($copy);
    }
}
