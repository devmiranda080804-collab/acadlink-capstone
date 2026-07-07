<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
    public function index()
    {
        $myProgram = auth()->user()->program;

        // Faculty nakakakita lang ng announcements na target ang sariling program
        $announcements = Announcement::with(['user', 'programs'])
            ->whereHas('programs', fn($p) => $p->where('program', $myProgram))
            ->latest()
            ->get();

        return view('faculty.announcements', compact('announcements'));
    }
}