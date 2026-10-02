<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\TemplateDocument;

class TemplateController extends Controller
{
    // Faculty just views/downloads what their Program Head has distributed —
    // no uploading, no Google Docs anywhere in this pipeline
    public function index()
    {
        $myProgram = auth()->user()->program;

        $templates = TemplateDocument::with('creator')
            ->whereHas('programs', fn($p) => $p->where('program', $myProgram)->whereNotNull('distributed_at'))
            ->latest()
            ->get();

        return view('faculty.my-template', compact('templates'));
    }
}
