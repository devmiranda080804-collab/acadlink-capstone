<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\ContentModule;
use App\Models\FileVersion;
use App\Models\SystemDocument;
use App\Models\User;
use App\Services\DocumentTools;
use App\Services\GoogleDocsService;
use App\Services\OnlyOffice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;

// CMS: upload a file or start a blank one, then open and edit it inside AcadLink, alone or
// together with the faculty it's shared with. Only the owner can share, rename or delete it.
class ContentModuleController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('faculty.cms', [
            'modules' => ContentModule::with(['shares.user', 'lastEditor'])->where('created_by', $user->id)->latest()->get(),
            'sharedModules' => ContentModule::with(['creator', 'lastEditor'])
                ->whereHas('shares', fn ($q) => $q->where('user_id', $user->id))
                ->latest()
                ->get(),
            'colleagues' => $this->colleagues(),
            'officialDocuments' => SystemDocument::where('is_published', true)->latest()->get(),
            'editorReady' => OnlyOffice::isConfigured(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'mode'        => 'required|in:blank,upload',
            'blank_type'  => ['required_if:mode,blank', 'nullable', Rule::in(array_keys(ContentModule::BLANK_TYPES))],
            'file'        => 'required_if:mode,upload|nullable|file|mimes:' . implode(',', ContentModule::UPLOAD_TYPES) . '|max:20480',
        ], [], ['blank_type' => 'Document type']);

        if ($request->mode === 'upload') {
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            $path = $file->storeAs('content-modules', $file->hashName(), 'public');
            $name = $file->getClientOriginalName();
        } else {
            $extension = $request->blank_type;
            $path = 'content-modules/' . bin2hex(random_bytes(20)) . '.' . $extension;
            Storage::disk('public')->put($path, file_get_contents(resource_path("templates/blank/blank.{$extension}")));
            $name = $request->title . '.' . $extension;
        }

        $module = ContentModule::create([
            'created_by'  => auth()->id(),
            'title'       => $request->title,
            'description' => $request->description,
            'file_path'   => $path,
            'file_name'   => $name,
            'file_type'   => $extension,
            'file_size'   => Storage::disk('public')->size($path),
        ]);

        return redirect('/faculty/cms/' . $module->id . '/edit');
    }

    public function edit(ContentModule $module)
    {
        $user = auth()->user();
        abort_unless($module->canView($user), 403, 'You do not have access to this document.');

        if ($module->isGoogleDoc()) {
            return redirect()->away($module->google_edit_url);
        }

        if (!OnlyOffice::isConfigured()) {
            return redirect('/faculty/cms')->withErrors(['editor' => 'The document editor is not available right now. You can still download the file.']);
        }

        if ($module->isWritten() && !$this->convertWrittenModule($module)) {
            return redirect('/faculty/cms')->withErrors(['editor' => 'This older module could not be opened in the editor.']);
        }

        return view('documents.editor', [
            'title'     => $module->title,
            'backUrl'   => url('/faculty/cms'),
            'backLabel' => 'Back to CMS',
            'canEdit'   => $module->canEdit($user),
            'config'    => (new OnlyOffice())->editorConfig($module, $user),
            'scriptUrl' => OnlyOffice::scriptUrl(),
        ]);
    }

    public function update(Request $request, ContentModule $module)
    {
        abort_unless($module->created_by === auth()->id(), 403);

        $module->update($request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]));

        return back()->with('success', 'Details saved.');
    }

    // Replaces the people this document is shared with. shares[user id] is edit, view or none.
    public function share(Request $request, ContentModule $module)
    {
        abort_unless($module->created_by === auth()->id(), 403);

        $request->validate([
            'shares'   => 'nullable|array',
            'shares.*' => 'in:edit,view,none',
        ]);

        $allowed = $this->colleagues()->pluck('id')->all();
        $chosen = collect($request->input('shares', []))
            ->filter(fn ($access, $userId) => $access !== 'none' && in_array((int) $userId, $allowed, true));

        $module->shares()->whereNotIn('user_id', $chosen->keys())->delete();
        foreach ($chosen as $userId => $access) {
            $module->shares()->updateOrCreate(['user_id' => $userId], ['can_edit' => $access === 'edit']);
        }

        return back()->with('success', $chosen->isEmpty() ? 'This document is no longer shared.' : 'Sharing updated.');
    }

    public function versions(ContentModule $module, DocumentTools $tools)
    {
        abort_unless($module->canEdit(auth()->user()), 403);

        return response()->json($tools->history($module, '/faculty/cms/' . $module->id . '/versions'));
    }

    public function restoreVersion(ContentModule $module, FileVersion $version)
    {
        abort_unless($module->canEdit(auth()->user()), 403);

        $module->restoreVersion($version, auth()->user());

        return back()->with('success', 'Version restored.');
    }

    // Makes the viewer's own editable Word copy of a PDF or old .doc file.
    public function convert(ContentModule $module, DocumentTools $tools)
    {
        abort_unless($module->canView(auth()->user()) && $module->canBeConverted(), 403);

        $contents = $tools->convertToWord($module);
        if ($contents === null) {
            return response()->json(['status' => 'working']);
        }

        $copy = ContentModule::create([
            'created_by'  => auth()->id(),
            'title'       => $module->title . ' (Word copy)',
            'description' => $module->description,
        ] + $tools->storeWordCopy($contents, $module, 'content-modules'));

        return response()->json(['redirect' => url('/faculty/cms/' . $copy->id . '/edit')]);
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

        return back()->with('success', 'Document deleted.');
    }

    // Faculty in the same program can work together on a document.
    protected function colleagues()
    {
        $user = auth()->user();

        return User::where('role', 'faculty')
            ->where('program', $user->program)
            ->where('id', '!=', $user->id)
            ->whereNull('archived_at')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }

    // Modules written in the old text editor become Word files the first time they're opened.
    protected function convertWrittenModule(ContentModule $module): bool
    {
        try {
            $phpWord = new PhpWord();
            Html::addHtml($phpWord->addSection(), (string) $module->content);

            $temporary = tempnam(sys_get_temp_dir(), 'cms');
            IOFactory::createWriter($phpWord, 'Word2007')->save($temporary);

            $path = 'content-modules/' . bin2hex(random_bytes(20)) . '.docx';
            Storage::disk('public')->put($path, file_get_contents($temporary));
            unlink($temporary);
        } catch (\Throwable $e) {
            report($e);

            return false;
        }

        $module->update([
            'content'   => null,
            'file_path' => $path,
            'file_name' => $module->title . '.docx',
            'file_type' => 'docx',
            'file_size' => Storage::disk('public')->size($path),
        ]);

        return true;
    }
}
