<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\FileVersion;
use App\Models\Setting;
use App\Models\SystemDocument;
use App\Models\TemplateType;
use App\Services\DocumentTools;
use App\Services\OnlyOffice;
use App\Support\SiteContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

// The CMS of the system itself, separate from the faculty's own content. Documents are managed
// by the Admin and the Secretary; Site Content (wording, images, letterhead) by the Admin.
class SystemCmsController extends Controller
{
    const UPLOAD_TYPES = ['docx', 'xlsx', 'pptx', 'pdf', 'doc'];

    const BLANK_TYPES = [
        'docx' => 'Document (Word)',
        'xlsx' => 'Spreadsheet (Excel)',
        'pptx' => 'Presentation (PowerPoint)',
    ];

    public function index()
    {
        return view('system-cms.index', [
            'base'        => $this->base(),
            'isAdmin'     => auth()->user()->role === 'admin',
            'templateTypes' => TemplateType::orderBy('id')->get(),
            'templateTypeUsage' => TemplateType::usageCounts(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'mode'        => 'required|in:upload,blank',
            'blank_type'  => ['required_if:mode,blank', 'nullable', Rule::in(array_keys(self::BLANK_TYPES))],
            'file'        => 'required_if:mode,upload|nullable|file|mimes:' . implode(',', self::UPLOAD_TYPES) . '|max:20480',
        ], [], ['blank_type' => 'Document type']);

        if ($request->mode === 'upload') {
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            $path = $file->storeAs('system-documents', $file->hashName(), 'public');
            $name = $file->getClientOriginalName();
        } else {
            $extension = $request->blank_type;
            $path = 'system-documents/' . bin2hex(random_bytes(20)) . '.' . $extension;
            Storage::disk('public')->put($path, file_get_contents(resource_path("templates/blank/blank.{$extension}")));
            $name = $request->title . '.' . $extension;
        }

        $document = SystemDocument::create([
            'created_by'  => auth()->id(),
            'title'       => $request->title,
            'description' => $request->description,
            'file_path'   => $path,
            'file_name'   => $name,
            'file_type'   => $extension,
            'file_size'   => Storage::disk('public')->size($path),
        ]);

        AuditLog::record('System Document Added', "{$document->title} added by " . auth()->user()->name);

        return OnlyOffice::isConfigured()
            ? redirect($this->base() . '/documents/' . $document->id . '/edit')
            : redirect($this->base())->with('success', 'Document added.');
    }

    public function edit(SystemDocument $document)
    {
        $user = auth()->user();

        if (!OnlyOffice::isConfigured()) {
            return redirect($this->base())->withErrors(['editor' => 'The document editor is not available right now. You can still download the file.']);
        }

        return view('documents.editor', [
            'title'     => $document->title,
            'backUrl'   => url($this->base()),
            'backLabel' => 'Back to System CMS',
            'canEdit'   => $document->canEdit($user),
            'config'    => (new OnlyOffice())->editorConfig($document, $user),
            'scriptUrl' => OnlyOffice::scriptUrl(),
        ]);
    }

    public function update(Request $request, SystemDocument $document)
    {
        $document->update($request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]));

        return back()->with('success', 'Details saved.');
    }

    public function versions(SystemDocument $document, DocumentTools $tools)
    {
        return response()->json($tools->history($document, $this->base() . '/documents/' . $document->id . '/versions'));
    }

    public function restoreVersion(SystemDocument $document, FileVersion $version)
    {
        $document->restoreVersion($version, auth()->user());

        AuditLog::record('System Document Updated', "An earlier version of {$document->title} restored by " . auth()->user()->name);

        return back()->with('success', 'Version restored.');
    }

    public function convert(SystemDocument $document, DocumentTools $tools)
    {
        abort_unless($document->canBeConverted(), 403);

        $contents = $tools->convertToWord($document);
        if ($contents === null) {
            return response()->json(['status' => 'working']);
        }

        $copy = SystemDocument::create([
            'created_by'  => auth()->id(),
            'title'       => $document->title . ' (Word copy)',
            'description' => $document->description,
        ] + $tools->storeWordCopy($contents, $document, 'system-documents'));

        AuditLog::record('System Document Added', "{$copy->title} converted by " . auth()->user()->name);

        return response()->json(['redirect' => url($this->base() . '/documents/' . $copy->id . '/edit')]);
    }

    public function togglePublish(SystemDocument $document)
    {
        $document->update(['is_published' => !$document->is_published]);

        AuditLog::record('System Document Updated', $document->title . ($document->is_published ? ' made visible to all' : ' hidden') . ' by ' . auth()->user()->name);

        return back()->with('success', $document->is_published ? 'Now visible to all users.' : 'Hidden. Only the Admin can open it now.');
    }

    public function destroy(SystemDocument $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $title = $document->title;
        $document->delete();

        AuditLog::record('System Document Deleted', "{$title} deleted by " . auth()->user()->name);

        return back()->with('success', 'Document deleted.');
    }

    public function updateSite(Request $request)
    {
        $rules = [];
        foreach (SiteContent::TEXTS as $key => $text) {
            $rules[$key] = 'required|string|max:' . (($text['multiline'] ?? false) ? 500 : 150);
        }
        foreach (SiteContent::IMAGES as $key => $image) {
            $rules['image_' . $key] = 'nullable|file|mimes:' . (($image['png_only'] ?? false) ? 'png' : 'jpg,jpeg,png') . '|max:5120';
        }
        $names = collect(SiteContent::TEXTS)->map(fn ($text) => $text['label'])->all()
            + collect(SiteContent::IMAGES)->mapWithKeys(fn ($image, $key) => ['image_' . $key => $image['label']])->all();
        $request->validate($rules, [], $names);

        foreach (array_keys(SiteContent::TEXTS) as $key) {
            Setting::put('site.' . $key, trim($request->input($key)));
        }

        foreach (array_keys(SiteContent::IMAGES) as $key) {
            if ($request->hasFile('image_' . $key)) {
                $old = Setting::get('site.image.' . $key);
                Setting::put('site.image.' . $key, $request->file('image_' . $key)->store('site-content', 'public'));
                if ($old) {
                    Storage::disk('public')->delete($old);
                }
            } elseif ($request->boolean('reset_' . $key)) {
                if ($old = Setting::get('site.image.' . $key)) {
                    Storage::disk('public')->delete($old);
                }
                Setting::put('site.image.' . $key, null);
            }
        }

        SiteContent::flush();

        AuditLog::record('Site Content Updated', 'System content updated by ' . auth()->user()->name);

        return back()->with(['success' => 'Site content saved.', 'site_saved' => true]);
    }

    // Public, because the login page shows these before anyone has signed in.
    public function image(Request $request, string $key)
    {
        abort_unless(isset(SiteContent::IMAGES[$key]), 404);

        $path = $request->boolean('original') ? SiteContent::originalPath($key) : SiteContent::imagePath($key);

        return response()->file($path, ['Cache-Control' => 'public, max-age=86400']);
    }

    protected function base(): string
    {
        return '/admin/system-cms';
    }
}
