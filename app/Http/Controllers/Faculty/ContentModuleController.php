<?php

namespace App\Http\Controllers\Faculty;

use Anthropic\Client;
use App\Http\Controllers\Controller;
use App\Models\ContentModule;
use App\Services\GoogleDocsService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;

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

    // Tries to turn an uploaded file's real content into editable HTML.
    // Returns null (never throws) when extraction isn't possible or fails —
    // the caller treats that as "store it as a plain file instead."
    protected function extractContentFromUpload(UploadedFile $file): ?string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'docx') {
            return $this->extractDocxContent($file->getRealPath());
        }

        if ($extension === 'pdf') {
            return $this->extractPdfContentViaAi($file->getRealPath());
        }

        // Legacy .doc (binary format) isn't reliably readable here.
        return null;
    }

    // Body content comes from mammoth.js (scripts/docx-to-html.js) — it reads the
    // .docx's real structure (headings, bold/italic, lists, tables, images) and
    // emits clean semantic HTML. Headers/footers aren't part of that body, so
    // their text is pulled separately via PhpWord and placed as plain blocks.
    //
    // Fails open, like extractPdfContentViaAi() — a malformed .docx or a missing
    // Node runtime should fall back to storing the upload as a plain file,
    // not 500 the whole request.
    protected function extractDocxContent(string $path): ?string
    {
        try {
            $body = $this->convertDocxBodyViaMammoth($path);
            if ($body === null) {
                return null;
            }

            $phpWord = IOFactory::load($path);
            $headerBlocks = $this->extractHeaderFooterText($phpWord, 'getHeaders');
            $footerBlocks = $this->extractHeaderFooterText($phpWord, 'getFooters');

            if ($headerBlocks) {
                $body = '<div>' . implode('', array_map(fn($b) => '<p><em>' . e($b) . '</em></p>', $headerBlocks)) . '</div><hr>' . $body;
            }
            if ($footerBlocks) {
                $body .= '<hr><div>' . implode('', array_map(fn($b) => '<p><em>' . e($b) . '</em></p>', $footerBlocks)) . '</div>';
            }

            return '<div class="docx-content">' . $body . '</div>';
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    protected function convertDocxBodyViaMammoth(string $path): ?string
    {
        $result = Process::timeout(60)->run(['node', base_path('scripts/docx-to-html.js'), $path]);

        if (!$result->successful()) {
            report(new \RuntimeException('docx-to-html failed: ' . $result->errorOutput()));
            return null;
        }

        $html = trim($result->output());

        return $html === '' ? null : $html;
    }

    // Returns one text block per top-level header/footer element (each
    // paragraph, or each cell of a letterhead table) instead of one long
    // joined string — so a two-column letterhead (e.g. university name on
    // the left, college name on the right) comes out as separate lines
    // instead of being run together into one unreadable sentence.
    protected function extractHeaderFooterText(\PhpOffice\PhpWord\PhpWord $phpWord, string $getter): array
    {
        $blocks = [];

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->$getter() as $headerFooter) {
                foreach ($headerFooter->getElements() as $element) {
                    if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                        foreach ($element->getRows() as $row) {
                            foreach ($row->getCells() as $cell) {
                                foreach ($cell->getElements() as $cellElement) {
                                    $text = $this->flattenElementText($cellElement);
                                    if ($text !== '') {
                                        $blocks[] = $text;
                                    }
                                }
                            }
                        }
                        continue;
                    }

                    $text = $this->flattenElementText($element);
                    if ($text !== '') {
                        $blocks[] = $text;
                    }
                }
            }
        }

        return $blocks;
    }

    protected function flattenElementText($element): string
    {
        if (method_exists($element, 'getText')) {
            $t = $element->getText();

            return is_string($t) ? trim($t) : '';
        }

        if (method_exists($element, 'getElements')) {
            $parts = [];
            foreach ($element->getElements() as $child) {
                $childText = $this->flattenElementText($child);
                if ($childText !== '') {
                    $parts[] = $childText;
                }
            }

            return trim(implode(' ', $parts));
        }

        return '';
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
