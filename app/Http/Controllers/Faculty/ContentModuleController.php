<?php

namespace App\Http\Controllers\Faculty;

use Anthropic\Client;
use App\Http\Controllers\Controller;
use App\Models\ContentModule;
use App\Services\GoogleDocsService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
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

    // Uses PhpWord's own HTML writer (load the .docx, write it back out as
    // HTML) instead of flattening to plain text — this keeps the upload
    // looking like itself once opened: bold/italic, tables, etc. survive,
    // not just the bare words.
    //
    // Fails open, like extractPdfContentViaAi() — a malformed .docx or an
    // element PhpWord's HTML writer can't handle should fall back to storing
    // the upload as a plain file, not 500 the whole request.
    protected function extractDocxContent(string $path): ?string
    {
        try {
            $phpWord = IOFactory::load($path);
            $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');

            ob_start();
            $htmlWriter->save('php://output');
            $fullHtml = ob_get_clean();

            if (!preg_match('#<body[^>]*>(.*)</body>#is', $fullHtml, $matches)) {
                return null;
            }

            // Drop PhpWord's own page-wrapper <div>s, keep everything inside them
            $body = trim(preg_replace('#</?div[^>]*>#i', '', $matches[1]));

            // PhpWord's HTML writer silently drops headers/footers — HTML has no
            // "repeat on every printed page" concept to translate them into. Pull
            // their text out separately so a letterhead/footer isn't just lost,
            // placed as plain blocks at the top/bottom instead of being "sticky."
            $headerText = $this->extractHeaderFooterText($phpWord, 'getHeaders');
            $footerText = $this->extractHeaderFooterText($phpWord, 'getFooters');

            if ($headerText) {
                $body = '<p><em>' . e($headerText) . '</em></p><hr>' . $body;
            }
            if ($footerText) {
                $body .= '<hr><p><em>' . e($footerText) . '</em></p>';
            }

            if ($body === '') {
                return null;
            }

            // PhpWord puts the actual formatting — fonts, table borders, heading
            // sizes/colors, paragraph spacing — in a <style> block of CSS
            // rules (tags/classes), not inline on each element. Keeping only
            // the <body> (as before) silently threw all of that away, which is
            // why tables/headings/spacing looked flattened once uploaded.
            // Scope it to a wrapper class so these document-wide rules (e.g.
            // a bare "body {...}") can never leak into whatever page ends up
            // hosting this saved content later.
            $style = '';
            if (preg_match('#<style[^>]*>(.*?)</style>#is', $fullHtml, $styleMatch)) {
                $style = '<style>' . $this->scopeDocxCss($styleMatch[1], 'docx-content') . '</style>';
            }

            return $style . '<div class="docx-content">' . $body . '</div>';
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    // Rewrites PhpWord's document-wide CSS (written for a full standalone HTML
    // page — bare "body"/"h1"/"table" selectors) so every rule only applies
    // inside the given wrapper class.
    protected function scopeDocxCss(string $css, string $scopeClass): string
    {
        // @page is print-layout only, meaningless on screen, and isn't a
        // normal selector that can be scoped the same way.
        $css = preg_replace('#@page\b[^{]*\{[^}]*\}#i', '', $css) ?? $css;

        // PhpWord has a known unit-conversion bug on some paragraph styles
        // (e.g. "List Paragraph") that emits an absurd margin like "360in" —
        // clamp any inch-based margin so one malformed rule can't blow out
        // the whole layout.
        $css = preg_replace_callback(
            '#(margin(?:-left|-right|-top|-bottom)?\s*:\s*)(\d+(?:\.\d+)?)in#i',
            fn($m) => $m[1] . min((float) $m[2], 2) . 'in',
            $css
        ) ?? $css;

        return preg_replace_callback('#([^{}]+)\{([^{}]*)\}#', function ($m) use ($scopeClass) {
            $scoped = array_map(function ($selector) use ($scopeClass) {
                $selector = trim($selector);

                return ($selector === 'body' || $selector === '*')
                    ? '.' . $scopeClass
                    : '.' . $scopeClass . ' ' . $selector;
            }, explode(',', $m[1]));

            return implode(', ', $scoped) . '{' . $m[2] . '}';
        }, $css) ?? $css;
    }

    protected function extractHeaderFooterText(\PhpOffice\PhpWord\PhpWord $phpWord, string $getter): string
    {
        $text = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->$getter() as $headerFooter) {
                foreach ($headerFooter->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $t = $element->getText();
                        $text .= (is_string($t) ? $t : '') . ' ';
                    } elseif (method_exists($element, 'getElements')) {
                        foreach ($element->getElements() as $child) {
                            if (method_exists($child, 'getText')) {
                                $childText = $child->getText();
                                $text .= (is_string($childText) ? $childText : '') . ' ';
                            }
                        }
                    }
                }
            }
        }

        return trim($text);
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
