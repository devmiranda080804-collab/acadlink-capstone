<?php

namespace App\Http\Controllers\ProgramHead;

use App\Http\Controllers\Controller;
use App\Models\SystemDocument;

// The documents the college published in the System CMS. Read-only for the Program Head.
class OfficialDocumentController extends Controller
{
    public function index()
    {
        return view('program-head.official-documents', [
            'documents' => SystemDocument::where('is_published', true)->latest()->get(),
        ]);
    }
}
