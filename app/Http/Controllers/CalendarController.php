<?php

namespace App\Http\Controllers;

use App\Models\CalendarActivity;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    // Sino ang pwedeng mag-manage (add/edit/delete)
    protected array $canManage = ['admin', 'secretary'];

    // View — lahat ng roles, dini-detect ang tamang blade
    public function index()
    {
        $role = auth()->user()->role;

        // Lahat ng activities — JSON para sa JS calendar
        $activities = CalendarActivity::with('creator')
            ->orderBy('activity_date')
            ->get()
            ->map(function ($a) {
                return [
                    'id'          => $a->id,
                    'title'       => $a->title,
                    'description' => $a->description,
                    'date'        => $a->activity_date->format('Y-m-d'),
                    'location'    => $a->location,
                    'category'    => $a->category,
                    'categoryLabel' => $a->category_label,
                    'creator'     => $a->creator->name ?? 'Unknown',
                ];
            });

        $canManage = in_array($role, $this->canManage);

        // Piliin ang tamang blade base sa role
        $viewMap = [
            'admin'        => 'admin.calendar',
            'program_head' => 'program-head.calendar',
            'secretary'    => 'secretary.calendar',
            'faculty'      => 'faculty.calendar',
        ];

        return view($viewMap[$role], compact('activities', 'canManage'));
    }

    public function store(Request $request)
    {
        abort_unless(in_array(auth()->user()->role, $this->canManage), 403);

        $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'activity_date' => 'required|date',
            'location'      => 'nullable|string|max:255',
            'category'      => 'required|in:general,exam,faculty,holiday',
        ]);

        CalendarActivity::create([
            'created_by'    => auth()->id(),
            'title'         => $request->title,
            'description'   => $request->description,
            'activity_date' => $request->activity_date,
            'location'      => $request->location,
            'category'      => $request->category,
        ]);

        return back()->with('success', 'Activity added successfully.');
    }

    public function update(Request $request, CalendarActivity $activity)
    {
        abort_unless(in_array(auth()->user()->role, $this->canManage), 403);

        $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'activity_date' => 'required|date',
            'location'      => 'nullable|string|max:255',
            'category'      => 'required|in:general,exam,faculty,holiday',
        ]);

        $activity->update($request->only([
            'title', 'description', 'activity_date', 'location', 'category',
        ]));

        return back()->with('success', 'Activity updated successfully.');
    }

    public function destroy(CalendarActivity $activity)
    {
        abort_unless(in_array(auth()->user()->role, $this->canManage), 403);

        $activity->delete();

        return back()->with('success', 'Activity deleted.');
    }
}