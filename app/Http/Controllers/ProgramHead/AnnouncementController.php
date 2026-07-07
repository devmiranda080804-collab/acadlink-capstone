<?php

namespace App\Http\Controllers\ProgramHead;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $myProgram = auth()->user()->program;

        $announcements = Announcement::with(['user', 'programs'])
            ->where(function ($q) use ($myProgram) {
                // Admin posts na target ang program ng PH
                $q->whereHas('user', fn($u) => $u->where('role', 'admin'))
                  ->whereHas('programs', fn($p) => $p->where('program', $myProgram));
            })
            ->orWhere(function ($q) use ($myProgram) {
                // Secretary posts na target ang program ng PH
                $q->whereHas('user', fn($u) => $u->where('role', 'secretary'))
                  ->whereHas('programs', fn($p) => $p->where('program', $myProgram));
            })
            ->orWhere(function ($q) {
                // Sariling posts ng PH
                $q->where('user_id', auth()->id());
            })
            ->latest()
            ->get();

        return view('program-head.ph-announcements', compact('announcements', 'myProgram'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'required|string',
        ]);

        // PH locked sa sariling program — automatic, hindi pumipili
        $announcement = Announcement::create([
            'user_id' => auth()->id(),
            'title'   => $request->title,
            'body'    => $request->body,
        ]);

        $announcement->programs()->create(['program' => auth()->user()->program]);

        return back()->with('success', 'Announcement posted successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        abort_unless($announcement->user_id === auth()->id(), 403);
        $announcement->delete();
        return back()->with('success', 'Announcement deleted.');
    }
}