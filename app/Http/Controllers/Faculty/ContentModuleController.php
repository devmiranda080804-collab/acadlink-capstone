<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\ContentModule;
use App\Services\GoogleDocsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContentModuleController extends Controller
{
    // Faculty's own content/module library — each item is created, edited,
    // and deleted only by the faculty member who made it
    public function index()
    {
        $modules = ContentModule::where('created_by', auth()->id())
            ->latest()
            ->get();

        return view('faculty.cms', compact('modules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'mode'        => 'required|in:google_doc,upload_file',
            'file'        => 'required_if:mode,upload_file|nullable|file|mimes:pdf,doc,docx|max:20480',
        ]);

        $data = [
            'created_by'  => auth()->id(),
            'title'       => $request->title,
            'description' => $request->description,
        ];

        if ($request->mode === 'google_doc') {
            $google = new GoogleDocsService();
            $data['google_doc_id'] = $google->createDocument($request->title);

            if (auth()->user()->google_email) {
                $google->shareWithEmail($data['google_doc_id'], auth()->user()->google_email, 'writer');
            }
        } else {
            $file = $request->file('file');
            $path = $file->store('content-modules', 'public');

            $data['file_path'] = $path;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = $file->getClientOriginalExtension();
            $data['file_size'] = $file->getSize();
        }

        ContentModule::create($data);

        return back()->with('success', 'Module created.');
    }

    public function update(Request $request, ContentModule $module)
    {
        abort_unless($module->created_by === auth()->id(), 403);

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $module->update($request->only('title', 'description'));

        return back()->with('success', 'Module updated.');
    }

    public function destroy(ContentModule $module)
    {
        abort_unless($module->created_by === auth()->id(), 403);

        if ($module->file_path) {
            Storage::disk('public')->delete($module->file_path);
        }

        if ($module->google_doc_id) {
            (new GoogleDocsService())->deleteDocument($module->google_doc_id);
        }

        $module->delete();

        return back()->with('success', 'Module deleted.');
    }
}
