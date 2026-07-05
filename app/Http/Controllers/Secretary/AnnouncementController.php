<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('user')
            ->where(function ($q) {
                // Admin posts
                $q->whereHas('user', fn($u) => $u->where('role', 'admin'))
                  ->where('visibility', 'all');
            })
            ->orWhere(function ($q) {
                // Program Head posts (lahat ng program)
                $q->whereHas('user', fn($u) => $u->where('role', 'program_head'))
                  ->where('visibility', 'program');
            })
            ->orWhere(function ($q) {
                // Sariling posts ng Secretary
                $q->where('user_id', auth()->id());
            })
            ->latest()
            ->get();

        return view('secretary.sec-announcements', compact('announcements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'required|string',
            'tag'   => 'required|in:general,priority,faculty,urgent',
        ]);

        Announcement::create([
            'user_id'    => auth()->id(),
            'title'      => $request->title,
            'body'       => $request->body,
            'tag'        => $request->tag,
            'visibility' => 'all', // Secretary posts visible sa lahat
            'program'    => null,
        ]);

        return back()->with('success', 'Announcement posted successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        abort_unless($announcement->user_id === auth()->id(), 403);
        $announcement->delete();
        return back()->with('success', 'Announcement deleted.');
    }
}