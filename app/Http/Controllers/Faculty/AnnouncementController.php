<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
    public function index()
    {
        $myProgram = auth()->user()->program;

        $announcements = Announcement::with('user')
            ->where(function ($q) {
                // Admin posts
                $q->whereHas('user', fn($u) => $u->where('role', 'admin'))
                  ->where('visibility', 'all');
            })
            ->orWhere(function ($q) {
                // Secretary posts
                $q->whereHas('user', fn($u) => $u->where('role', 'secretary'))
                  ->where('visibility', 'all');
            })
            ->orWhere(function ($q) use ($myProgram) {
                // PH posts — sariling program lang ng faculty
                $q->whereHas('user', fn($u) => $u->where('role', 'program_head'))
                  ->where('visibility', 'program')
                  ->where('program', $myProgram);
            })
            ->latest()
            ->get();

        return view('faculty.announcements', compact('announcements'));
    }
}