<?php

namespace App\Http\Controllers\Faculty;

use Anthropic\Client;
use App\Http\Controllers\Controller;
use App\Models\ContentModule;
use App\Services\DocxTextEditor;
use App\Services\GoogleDocsService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
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
            'mode'        => 'required|in:write,upload_file',
            'content'     => 'required_if:mode,write|nullable|string',
            'file'        => 'required_if:mode,upload_file|nullable|file|mimes:pdf,doc,docx|max:20480',
        ]);

        $data = [
            'created_by'  => auth()->id(),
            'title'       => $request->title,
            'description' => $request->description,
        ];

        if ($request->mode === 'write') {
            // Written directly in AcadLink via the WYSIWYG editor — no Google
            // Docs involved, per the adviser's clarification that CMS content
            // should be editable inside the web application itself.
            $data['content'] = $this->sanitizeHtml($request->content);
        } else {
            $file = $request->file('file');

            // Like importing a file into Google Docs — try to turn the
            // upload's actual content into something editable right here,
            // instead of just storing it as a static, un-editable blob.
            $extracted = $this->extractContentFromUpload($file);

            if ($extracted !== null) {
                $data['content'] = $this->sanitizeHtml($extracted);
            } else {
                // Couldn't make it editable (legacy .doc, a scanned/image-only
                // PDF, or the AI extraction was unavailable) — falls back to
                // the honest behavior: stored as a plain, viewable file.
                $path = $file->store('content-modules', 'public');

                $data['file_path'] = $path;
                $data['file_name'] = $file->getClientOriginalName();
                $data['file_type'] = $file->getClientOriginalExtension();
                $data['file_size'] = $file->getSize();
            }
        }

        ContentModule::create($data);

        return back()->with('success', 'Module created.');
    }

    // Only PDFs get turned into editable HTML here. A .docx is kept as the original
    // file (see DocxTextEditor), and legacy .doc isn't reliably readable — both
    // return null, which the caller stores as a plain file.
    protected function extractContentFromUpload(UploadedFile $file): ?string
    {
        if (strtolower($file->getClientOriginalExtension()) === 'pdf') {
            return $this->extractPdfContentViaAi($file->getRealPath());
        }

        return null;
    }

    // Uses the same Claude integration already wired up elsewhere in the app
    // (there's no PDF text-extraction library in this project) to transcribe
    // a PDF's actual content into clean HTML. Fails open — any trouble here
    // just means the upload falls back to being a plain file instead.
    protected function extractPdfContentViaAi(string $path): ?string
    {
        $apiKey = config('services.anthropic.api_key');
        if (!$apiKey) {
            return null;
        }

        try {
            $client = new Client(apiKey: $apiKey);

            $message = $client->messages->create(
                model: 'claude-haiku-4-5',
                maxTokens: 4096,
                system: 'You transcribe the text content of a document into clean HTML, preserving '
                    . 'its structure (headings, paragraphs, lists, tables) using only these tags: '
                    . 'p, h1, h2, h3, ul, ol, li, strong, em, table, tr, td, th. Output raw HTML only '
                    . '— no markdown code fences, no <html>/<body> wrapper, no commentary.',
                messages: [[
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'document',
                            'source' => [
                                'type' => 'base64',
                                'media_type' => 'application/pdf',
                                'data' => base64_encode(file_get_contents($path)),
                            ],
                        ],
                        ['type' => 'text', 'text' => "Transcribe this document's content as HTML."],
                    ],
                ]],
            );

            foreach ($message->content as $block) {
                if ($block->type === 'text') {
                    $html = trim($block->text);
                    return $html === '' ? null : $html;
                }
            }

            return null;
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    public function update(Request $request, ContentModule $module)
    {
        abort_unless($module->created_by === auth()->id(), 403);

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'content'     => 'nullable|string',
        ]);

        $data = $request->only('title', 'description');

        // Only written (WYSIWYG) modules ever send a content field — file
        // uploads and legacy Google Docs keep editing their title/description only.
        if ($module->isWritten() && $request->has('content')) {
            $data['content'] = $this->sanitizeHtml($request->content);
        }

        $module->update($data);

        return back()->with('success', 'Module updated.');
    }

    public function docxFile(ContentModule $module)
    {
        abort_unless($module->created_by === auth()->id() && $module->isEditableDocx(), 403);

        return response()->file(Storage::disk('public')->path($module->file_path), [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Cache-Control' => 'no-store',
        ]);
    }

    public function docxText(ContentModule $module)
    {
        abort_unless($module->created_by === auth()->id() && $module->isEditableDocx(), 403);

        return response()->json([
            'paragraphs' => (new DocxTextEditor())->paragraphs(Storage::disk('public')->path($module->file_path)),
        ]);
    }

    public function updateDocxText(Request $request, ContentModule $module)
    {
        abort_unless($module->created_by === auth()->id() && $module->isEditableDocx(), 403);

        $request->validate([
            'paragraphs'   => 'required|array|min:1',
            'paragraphs.*' => 'nullable|string|max:5000',
        ]);

        $path = Storage::disk('public')->path($module->file_path);
        (new DocxTextEditor())->applyParagraphs($path, $request->input('paragraphs'));

        $module->update(['file_size' => Storage::disk('public')->size($module->file_path)]);

        return response()->json(['status' => 'ok']);
    }

    // Strips anything that could execute script in the editor's saved HTML
    // (<script> tags, inline event handlers, javascript: URLs) while leaving
    // Quill's normal formatting markup untouched — this is personal content
    // only ever rendered back to its own author, but still worth guarding.
    protected function sanitizeHtml(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }

        $html = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $html);
        $html = preg_replace('#\son\w+\s*=\s*(".*?"|\'.*?\'|[^\s>]+)#i', '', $html);
        $html = preg_replace('#(href|src)\s*=\s*(["\'])\s*javascript:[^"\']*\2#i', '$1=$2#$2', $html);

        return $html;
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
