<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        // Secretary nakakakita ng lahat (institutional oversight)
        $announcements = Announcement::with(['user', 'programs'])
            ->latest()
            ->get();

        return view('secretary.sec-announcements', compact('announcements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'body'       => 'required|string',
            'programs'   => 'required|array|min:1',
            'programs.*' => 'in:FMAD,OFD,BAD',
        ]);

        $announcement = Announcement::create([
            'user_id' => auth()->id(),
            'title'   => $request->title,
            'body'    => $request->body,
        ]);

        foreach ($request->programs as $program) {
            $announcement->programs()->create(['program' => $program]);
        }

        return back()->with('success', 'Announcement posted successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        abort_unless($announcement->user_id === auth()->id(), 403);
        $announcement->delete();
        return back()->with('success', 'Announcement deleted.');
    }
}