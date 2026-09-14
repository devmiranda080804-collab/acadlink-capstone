<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\TemplateElement;

class TemplateGuideController extends Controller
{
    // Read-only view of the template structure Admin defines in the CMS —
    // lets faculty see what each template type should contain before they submit one
    public function index()
    {
        $elementsByType = TemplateElement::active()
            ->orderBy('order')
            ->get()
            ->groupBy('template_type');

        return view('faculty.cms', compact('elementsByType'));
    }
}
