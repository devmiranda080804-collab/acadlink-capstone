<?php

namespace App\Http\Controllers\ProgramHead;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $myProgram = auth()->user()->program;

        $announcements = Announcement::with('user')
            ->where(function ($q) use ($myProgram) {
                // Admin posts (visibility = all)
                $q->whereHas('user', fn($u) => $u->where('role', 'admin'))
                  ->where('visibility', 'all');
            })
            ->orWhere(function ($q) use ($myProgram) {
                // Secretary posts (visibility = all)
                $q->whereHas('user', fn($u) => $u->where('role', 'secretary'))
                  ->where('visibility', 'all');
            })
            ->orWhere(function ($q) use ($myProgram) {
                // Sariling posts ng PH (same program)
                $q->where('user_id', auth()->id());
            })
            ->latest()
            ->get();

        return view('program-head.ph-announcements', compact('announcements'));
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
            'visibility' => 'program', // PH posts = program only
            'program'    => auth()->user()->program,
        ]);

        return back()->with('success', 'Announcement posted successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        // PH pwedeng mag-delete ng sarili niyang post lang
        abort_unless($announcement->user_id === auth()->id(), 403);
        $announcement->delete();
        return back()->with('success', 'Announcement deleted.');
    }
}