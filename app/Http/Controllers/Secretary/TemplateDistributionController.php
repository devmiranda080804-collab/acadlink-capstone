<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;

class TemplateDistributionController extends Controller
{
    public function index(Request $request)
    {
        // Secretary nakakakita ng lahat ng approved templates (lahat ng program)
        $query = Template::with(['faculty', 'distributor'])
            ->where('status', 'approved');

        if ($request->filled('program')) {
            $query->where('program', $request->program);
        }

        $templates = $query->latest()->get();

        return view('secretary.template-distribution', compact('templates'));
    }

    public function distribute(Request $request, Template $template)
    {
        // Approved lang ang pwedeng i-distribute
        abort_unless($template->status === 'approved', 403);

        $template->update([
            'distributed_at' => now(),
            'distributed_by' => auth()->id(),
        ]);

        return back()->with('success', 'Template distributed successfully. Available na ito sa faculty.');
    }

    public function undistribute(Request $request, Template $template)
    {
        abort_unless($template->status === 'approved', 403);

        $template->update([
            'distributed_at' => null,
            'distributed_by' => null,
        ]);

        return back()->with('success', 'Template distribution recalled.');
    }
}