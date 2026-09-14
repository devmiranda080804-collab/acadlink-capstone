<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\TemplateDocument;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class TemplateDistributionController extends Controller
{
    public function index()
    {
        // Secretary sees everything Admin has provided — pending and already-forwarded
        $templates = TemplateDocument::with(['creator', 'forwarder', 'programs'])
            ->latest()
            ->get();

        return view('secretary.template-distribution', compact('templates'));
    }

    public function forward(Request $request, TemplateDocument $template)
    {
        abort_if($template->isForwarded(), 403, 'This template has already been forwarded.');

        $template->update([
            'forwarded_by' => auth()->id(),
            'forwarded_at' => now(),
        ]);

        AuditLog::record('Template Forwarded', "{$template->title} forwarded to Program Heads by " . auth()->user()->name);

        return back()->with('success', 'Template forwarded to all Program Heads.');
    }
}
