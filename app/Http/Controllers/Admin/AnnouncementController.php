<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        // Admin can see all announcements
        $announcements = Announcement::with(['user', 'programs'])
            ->active()
            ->latest()
            ->get();

        Announcement::markReadBy($announcements, auth()->user());

        return view('admin.admin-announcements', compact('announcements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'body'       => 'required|string',
            'programs'   => 'required|array|min:1',
            'programs.*' => 'in:BSA,BSMA,BSOA',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $announcement = Announcement::create([
            'user_id'    => auth()->id(),
            'title'      => $request->title,
            'body'       => $request->body,
            'expires_at' => $request->expires_at,
        ]);

        foreach ($request->programs as $program) {
            $announcement->programs()->create(['program' => $program]);
        }

        return back()->with('success', 'Announcement posted successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete(); // programs are cascaded
        return back()->with('success', 'Announcement deleted.');
    }
}