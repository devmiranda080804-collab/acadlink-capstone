<?php

namespace App\Http\Controllers\ProgramHead;

use App\Http\Controllers\Controller;
use App\Models\TemplateDocument;

// Read-only: the Dean sends approved templates straight to programs, so the
// Program Head sees what their faculty received but no longer distributes.
class TemplateReviewController extends Controller
{
    public function index()
    {
        $myProgram = auth()->user()->program;

        $templates = TemplateDocument::with(['creator', 'reviewer', 'programs'])
            ->whereNull('superseded_at')
            ->whereHas('programs', fn($p) => $p->where('program', $myProgram)->whereNotNull('distributed_at'))
            ->latest()
            ->get();

        return view('program-head.template-review', compact('templates', 'myProgram'));
    }
}
