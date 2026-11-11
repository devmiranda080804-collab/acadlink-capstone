<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\FileVersion;
use App\Models\SharedResource;
use App\Services\DocumentTools;
use App\Services\OnlyOffice;
use App\Support\DisplayTime;
use App\Support\FileLinks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SharedLibraryController extends Controller
{
    public function index()
    {
        $myPrograms = auth()->user()->programCodes();
        $myProgram = implode(', ', $myPrograms);

        $items = collect();

        // Every item carries a "folder" (group/key/label/icon) so the view can arrange the
        // library into sections: one per program with whole-program resources by category,
        // and Courses with each course's materials and shared resources together.
        // Official templates stay in Templates; faculty share their edited copies here.

        // Course materials (own program) — one folder per actual course
        CourseMaterial::with(['course', 'uploader'])
            ->whereHas('course', fn($c) => $c->whereIn('program', $myPrograms))
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
                    'view_url'     => FileLinks::view('material', $m->id),
                    'download_url' => FileLinks::download('material', $m->id),
                    'date'         => $m->created_at,
                    'can_delete'   => false,
                    'folder_group' => 'courses',
                    'folder_key'   => 'course-' . $m->course_id,
                    'folder_label' => ($m->course->code ?? 'Unknown') . ' — ' . ($m->course->title ?? 'Course'),
                    'folder_icon'  => '📚',
                ]);
            });

        // Faculty-shared resources (own program) — in their course's folder, or one folder
        // per category when they're for the whole program
        $myCourses = SharedResource::coursesTaughtBy(auth()->user());
        $editorReady = OnlyOffice::isConfigured();

        SharedResource::with(['sharer', 'course', 'lastEditor'])
            ->whereIn('program', $myPrograms)
            ->get()
            ->each(function ($r) use (&$items, $myCourses, $editorReady) {
                $canEdit = $r->isEditableFile() && ($r->shared_by === auth()->id() || in_array($r->course_id, $myCourses, true));
                $folder = $r->course
                    ? ['folder_group' => 'courses', 'folder_key' => 'course-' . $r->course_id, 'folder_label' => $r->course->code . ' — ' . $r->course->title, 'folder_icon' => '📚']
                    : ['folder_group' => 'program-' . $r->program, 'folder_key' => 'shared-' . $r->program . '-' . $r->type, 'folder_label' => $r->type_label, 'folder_icon' => '🗂️'];

                $items->push($folder + [
                    'id'           => $r->id,
                    'source'       => 'shared',
                    'title'        => $r->title,
                    'type'         => $r->type_label,
                    'shared_by'    => $r->sharer->name ?? 'Unknown',
                    'desc'         => $r->description ?? 'Shared by faculty',
                    'file_type'    => $r->file_type,
                    'file_size'    => $r->readable_size,
                    'view_url'     => FileLinks::view('shared', $r->id),
                    'download_url' => FileLinks::download('shared', $r->id),
                    'date'         => $r->created_at,
                    'can_delete'   => $r->shared_by === auth()->id(), // own upload only
                    // Edited together in the editor by whoever shared it and the course's faculty.
                    'can_edit'     => $canEdit && $editorReady,
                    'edit_url'     => $editorReady && $r->isEditableFile() ? url('/faculty/shared-library/' . $r->id . '/edit') : null,
                    'history_url'  => $canEdit ? url('/faculty/shared-library/' . $r->id . '/versions') : null,
                    'edited'       => $r->last_edited_at ? 'Edited by ' . ($r->lastEditor->name ?? 'a former faculty member') . ' · ' . DisplayTime::format($r->last_edited_at) : null,
                    'together'     => $r->course ? 'Faculty teaching ' . $r->course->code . ' can edit this together.' : 'Only ' . ($r->shared_by === auth()->id() ? 'you' : ($r->sharer->name ?? 'the faculty member who shared it')) . ' can edit this.',
                ]);
            });

        $items = $items->sortByDesc('date')->values();

        // Newest files across every folder, so fresh uploads aren't buried inside one.
        $recentDays = 14;
        $recentItems = $items->filter(fn($i) => $i['date'] && $i['date']->gte(now()->subDays($recentDays)))->take(6)->values();

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

        $courses = Course::whereIn('program', $myPrograms)->orderBy('program')->orderBy('code')->get();

        // One section per program, then the course folders.
        $sections = collect($myPrograms)->mapWithKeys(fn ($code) => ['program-' . $code => 'Program — ' . $code])->all()
            + ['courses' => 'Courses'];

        return view('faculty.shared-library', compact('items', 'folders', 'myProgram', 'myPrograms', 'sections', 'recentItems', 'recentDays', 'courses'));
    }

    // Opens the shared file in the editor: to edit together, or to view.
    public function edit(SharedResource $resource)
    {
        $user = auth()->user();
        abort_unless($resource->canView($user), 403);

        if (!OnlyOffice::isConfigured()) {
            return redirect('/faculty/shared-library')->withErrors(['editor' => 'The document editor is not available right now. You can still view and download the file.']);
        }

        return view('documents.editor', [
            'title'     => $resource->title,
            'backUrl'   => url('/faculty/shared-library'),
            'backLabel' => 'Back to Shared Library',
            'canEdit'   => $resource->canEdit($user),
            'config'    => (new OnlyOffice())->editorConfig($resource, $user),
            'scriptUrl' => OnlyOffice::scriptUrl(),
        ]);
    }

    public function versions(SharedResource $resource, DocumentTools $tools)
    {
        abort_unless($resource->canEdit(auth()->user()), 403);

        return response()->json($tools->history($resource, '/faculty/shared-library/' . $resource->id . '/versions'));
    }

    public function restoreVersion(SharedResource $resource, FileVersion $version)
    {
        abort_unless($resource->canEdit(auth()->user()), 403);

        $resource->restoreVersion($version, auth()->user());

        return back()->with('success', 'Version restored.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'type'        => ['required', Rule::in(array_keys(SharedResource::typeOptions()))],
            'description' => 'nullable|string|max:500',
            'target'      => 'required|string',
            'file'        => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20480',
        ], [], ['target' => 'share to']);
        $target = SharedResource::resolveTarget($request->target, auth()->user()->programCodes());

        $file = $request->file('file');
        $path = $file->store('shared-resources', 'public');

        SharedResource::create([
            'shared_by'   => auth()->id(),
            'program'     => $target['program'],
            'course_id'   => $target['course_id'],
            'title'       => $request->title,
            'type'        => $request->type,
            'description' => $request->description,
            'file_path'   => $path,
            'file_name'   => $file->getClientOriginalName(),
            'file_type'   => $file->getClientOriginalExtension(),
            'file_size'   => $file->getSize(),
        ]);

        return back()->with('success', 'Resource shared with ' . $target['program'] . '.');
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