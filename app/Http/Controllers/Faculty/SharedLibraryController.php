<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\CourseMaterial;
use App\Models\SharedResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SharedLibraryController extends Controller
{
    public function index(Request $request)
    {
        $myProgram = auth()->user()->program;
        $filter = $request->get('filter', 'all'); // all, templates, materials, shared

        $items = collect();

        // 1. Distributed templates (sariling program)
        if ($filter === 'all' || $filter === 'templates') {
            Template::with('faculty')
                ->where('program', $myProgram)
                ->where('status', 'approved')
                ->whereNotNull('distributed_at')
                ->get()
                ->each(function ($t) use (&$items) {
                    $items->push([
                        'id'        => $t->id,
                        'source'    => 'template',
                        'title'     => $t->title,
                        'type'      => ucwords(str_replace('_', ' ', $t->type)),
                        'shared_by' => $t->faculty->name ?? 'Unknown',
                        'desc'      => 'Official distributed template',
                        'file_type' => $t->file_type,
                        'file_url'  => Storage::url($t->file_path),
                        'date'      => $t->distributed_at,
                        'can_delete'=> false,
                    ]);
                });
        }

        // 2. Course materials (sariling program)
        if ($filter === 'all' || $filter === 'materials') {
            CourseMaterial::with(['course', 'uploader'])
                ->whereHas('course', fn($c) => $c->where('program', $myProgram))
                ->get()
                ->each(function ($m) use (&$items) {
                    $items->push([
                        'id'        => $m->id,
                        'source'    => 'material',
                        'title'     => $m->title,
                        'type'      => 'Course Material',
                        'shared_by' => $m->uploader->name ?? 'Unknown',
                        'desc'      => $m->course->code ?? 'Course material',
                        'file_type' => $m->file_type,
                        'file_url'  => Storage::url($m->file_path),
                        'date'      => $m->created_at,
                        'can_delete'=> false,
                    ]);
                });
        }

        // 3. Faculty-shared resources (sariling program)
        if ($filter === 'all' || $filter === 'shared') {
            SharedResource::with('sharer')
                ->where('program', $myProgram)
                ->get()
                ->each(function ($r) use (&$items) {
                    $items->push([
                        'id'        => $r->id,
                        'source'    => 'shared',
                        'title'     => $r->title,
                        'type'      => 'Shared Resource',
                        'shared_by' => $r->sharer->name ?? 'Unknown',
                        'desc'      => $r->description ?? 'Shared by faculty',
                        'file_type' => $r->file_type,
                        'file_url'  => Storage::url($r->file_path),
                        'date'      => $r->created_at,
                        'can_delete'=> $r->shared_by === auth()->id(), // sariling upload lang
                    ]);
                });
        }

        $items = $items->sortByDesc('date')->values();

        return view('faculty.shared-library', compact('items', 'filter', 'myProgram'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'file'        => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20480',
        ]);

        $file = $request->file('file');
        $path = $file->store('shared-resources', 'public');

        SharedResource::create([
            'shared_by'   => auth()->id(),
            'program'     => auth()->user()->program,
            'title'       => $request->title,
            'description' => $request->description,
            'file_path'   => $path,
            'file_name'   => $file->getClientOriginalName(),
            'file_type'   => $file->getClientOriginalExtension(),
            'file_size'   => $file->getSize(),
        ]);

        return back()->with('success', 'Resource shared successfully sa iyong program.');
    }

    public function destroy(SharedResource $resource)
    {
        // Sariling upload lang ang pwedeng burahin
        abort_unless($resource->shared_by === auth()->id(), 403);

        Storage::disk('public')->delete($resource->file_path);
        $resource->delete();

        return back()->with('success', 'Resource removed.');
    }
}