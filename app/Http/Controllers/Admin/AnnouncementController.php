<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        // Admin nakakakita ng lahat ng announcements
        $announcements = Announcement::with('user')
            ->latest()
            ->get();

        return view('admin.admin-announcements', compact('announcements'));
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
            'body'        => $request->body,
            'tag'         => $request->tag,
            'visibility'  => 'all', // Admin posts visible sa lahat
            'program'     => null,
        ]);

        return back()->with('success', 'Announcement posted successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        // Admin pwedeng mag-delete ng kahit anong announcement
        $announcement->delete();

        return back()->with('success', 'Announcement deleted.');
    }
}