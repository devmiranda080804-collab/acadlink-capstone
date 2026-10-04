<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\TemplateDocument;
use App\Models\CourseMaterial;
use App\Models\SharedResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SharedLibraryController extends Controller
{
    public function index()
    {
        $myProgram = auth()->user()->program;

        $items = collect();

        // Every item carries a "folder" (group/key/label/icon) so the view can
        // arrange the library into real folders — Templates grouped by type,
        // Course Materials grouped by the actual course, Faculty Shared
        // grouped by category — instead of one long flat, mixed list.

        // 1. Templates distributed to this faculty member's program (current versions only)
        TemplateDocument::with('creator')
            ->whereNull('superseded_at')
            ->whereHas('programs', fn($p) => $p->where('program', $myProgram)->whereNotNull('distributed_at'))
            ->get()
            ->each(function ($t) use (&$items, $myProgram) {
                $items->push([
                    'id'           => $t->id,
                    'source'       => 'template',
                    'title'        => $t->title,
                    'type'         => TemplateDocument::typeLabel($t->type),
                    'shared_by'    => $t->creator->name ?? 'Unknown',
                    'desc'         => 'Official distributed template',
                    'file_type'    => $t->file_type,
                    'file_size'    => $t->readable_size,
                    'file_url'     => url("/faculty/shared-library/file/template/{$t->id}"),
                    'date'         => $t->programRow($myProgram)->distributed_at,
                    'can_delete'   => false,
                    'folder_group' => 'templates',
                    'folder_key'   => 'template-' . $t->type,
                    'folder_label' => TemplateDocument::typeLabel($t->type) . ' Templates',
                    'folder_icon'  => TemplateDocument::typeIcon($t->type),
                ]);
            });

        // 2. Course materials (own program) — one folder per actual course
        CourseMaterial::with(['course', 'uploader'])
            ->whereHas('course', fn($c) => $c->where('program', $myProgram))
            ->get()
            ->each(function ($m) use (&$items) {
                $items->push([
                    'id'           => $m->id,
                    'source'       => 'material',
                    'title'        => $m->title,
                    'type'         => 'Course Material',
                    'shared_by'    => $m->uploader->name ?? 'Unknown',
                    'desc'         => $m->course->code ?? 'Course material',
                    'file_type'    => $m->file_type,
                    'file_size'    => $m->readable_size,
                    'file_url'     => url("/faculty/shared-library/file/material/{$m->id}"),
                    'date'         => $m->created_at,
                    'can_delete'   => false,
                    'folder_group' => 'materials',
                    'folder_key'   => 'course-' . $m->course_id,
                    'folder_label' => ($m->course->code ?? 'Unknown') . ' — ' . ($m->course->title ?? 'Course'),
                    'folder_icon'  => '📚',
                ]);
            });

        // 3. Faculty-shared resources (own program) — one folder per category
        SharedResource::with('sharer')
            ->where('program', $myProgram)
            ->get()
            ->each(function ($r) use (&$items) {
                $items->push([
                    'id'           => $r->id,
                    'source'       => 'shared',
                    'title'        => $r->title,
                    'type'         => $r->type_label,
                    'shared_by'    => $r->sharer->name ?? 'Unknown',
                    'desc'         => $r->description ?? 'Shared by faculty',
                    'file_type'    => $r->file_type,
                    'file_size'    => $r->readable_size,
                    'file_url'     => url("/faculty/shared-library/file/shared/{$r->id}"),
                    'date'         => $r->created_at,
                    'can_delete'   => $r->shared_by === auth()->id(), // own upload only
                    'folder_group' => 'shared',
                    'folder_key'   => 'shared-' . $r->type,
                    'folder_label' => $r->type_label,
                    'folder_icon'  => '🗂️',
                ]);
            });

        $items = $items->sortByDesc('date')->values();

        // Build the folder list (one row per distinct folder_key), each
        // carrying its own item count — this is what renders as the
        // top-level "shelves" the faculty clicks into.
        $folders = $items->groupBy('folder_key')->map(function ($group) {
            $first = $group->first();

            return [
                'key'   => $first['folder_key'],
                'group' => $first['folder_group'],
                'label' => $first['folder_label'],
                'icon'  => $first['folder_icon'],
                'count' => $group->count(),
            ];
        })->sortBy('label')->values();

        return view('faculty.shared-library', compact('items', 'folders', 'myProgram'));
    }

    // Every file in the library is served through here instead of a raw public
    // storage URL, so access can actually be checked — a public URL would let
    // anyone (any program, even logged out) open a file just by having the
    // link, no matter what the listing above already filtered out.
    public function file(string $source, int $id)
    {
        $myProgram = auth()->user()->program;

        if ($source === 'template') {
            $template = TemplateDocument::findOrFail($id);
            abort_unless($template->isDistributedTo($myProgram), 403, 'This template has not been distributed to your program.');

            return Storage::disk('public')->response($template->file_path, $template->file_name);
        }

        if ($source === 'material') {
            $material = CourseMaterial::with('course')->findOrFail($id);
            abort_unless($material->course && $material->course->program === $myProgram, 403, 'This material belongs to a different program.');

            return Storage::disk('public')->response($material->file_path, $material->file_name);
        }

        if ($source === 'shared') {
            $resource = SharedResource::findOrFail($id);
            abort_unless($resource->program === $myProgram, 403, 'This resource belongs to a different program.');

            return Storage::disk('public')->response($resource->file_path, $resource->file_name);
        }

        abort(404);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'type'        => 'required|in:lecture_slides,case_study,activity_guide,assessment_sample',
            'description' => 'nullable|string|max:500',
            'file'        => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20480',
        ]);

        $file = $request->file('file');
        $path = $file->store('shared-resources', 'public');

        SharedResource::create([
            'shared_by'   => auth()->id(),
            'program'     => auth()->user()->program,
            'title'       => $request->title,
            'type'        => $request->type,
            'description' => $request->description,
            'file_path'   => $path,
            'file_name'   => $file->getClientOriginalName(),
            'file_type'   => $file->getClientOriginalExtension(),
            'file_size'   => $file->getSize(),
        ]);

        return back()->with('success', 'Resource shared successfully with your program.');
    }

    public function destroy(SharedResource $resource)
    {
        // Only own uploads can be deleted
        abort_unless($resource->shared_by === auth()->id(), 403);

        Storage::disk('public')->delete($resource->file_path);
        $resource->delete();

        return back()->with('success', 'Resource removed.');
    }
}