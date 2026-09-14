<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
    public function index()
    {
        // Faculty can only see announcements targeting their own program
        $announcements = Announcement::with(['user', 'programs'])
            ->active()
            ->visibleTo(auth()->user())
            ->latest()
            ->get();

        Announcement::markReadBy($announcements, auth()->user());

        return view('faculty.announcements', compact('announcements'));
    }
}