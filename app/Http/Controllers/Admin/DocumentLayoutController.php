<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Support\DocumentLayouts;
use App\Support\ExamPrintOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

// System CMS → Document Layouts (Admin only). Each column (TOS, Exam, Answer Key) saves on
// its own; the next download of that document uses the new header, contents and footer.
class DocumentLayoutController extends Controller
{
    // Form names can't hold dots, so "header.logo_left" is sent as "header__logo_left".
    public static function inputName(string $field): string
    {
        return str_replace('.', '__', $field);
    }

    public function update(Request $request, string $document)
    {
        abort_unless(isset(DocumentLayouts::DOCUMENTS[$document]), 404);
        $fields = DocumentLayouts::fields($document);
        $title = DocumentLayouts::DOCUMENTS[$document]['title'];

        if ($request->boolean('reset')) {
            foreach ($fields as $field => $meta) {
                if ($meta['type'] === 'image' && ($old = DocumentLayouts::uploadedImage($document, $field))) {
                    Storage::disk('public')->delete($old);
                }
                Setting::where('key', DocumentLayouts::key($document, $field))->delete();
            }
            DocumentLayouts::flush();
            AuditLog::record('Document Layout Updated', "{$title} layout reset to default by " . auth()->user()->name);

            return back()->with(['success' => "{$title} layout is back to the default.", 'cms_panel' => 'panel-layouts']);
        }

        $rules = [];
        $names = [];
        foreach ($fields as $field => $meta) {
            $name = self::inputName($field);
            $names[$name] = $meta['label'];
            $rules[$name] = match ($meta['type']) {
                'image'    => 'nullable|file|mimes:png|max:5120',
                'textarea' => 'nullable|string|max:500',
                'bool'     => 'nullable|boolean',
                'select'   => ['required', Rule::in(array_keys(ExamPrintOptions::choices($meta['options'])))],
                default    => 'nullable|string|max:200',
            };
        }
        $request->validate($rules, [], $names);

        foreach ($fields as $field => $meta) {
            $name = self::inputName($field);
            $key = DocumentLayouts::key($document, $field);

            if ($meta['type'] === 'image') {
                $old = DocumentLayouts::uploadedImage($document, $field);
                if ($request->hasFile($name)) {
                    Setting::put($key, $request->file($name)->store('document-layouts', 'public'));
                    if ($old) {
                        Storage::disk('public')->delete($old);
                    }
                } elseif ($request->boolean('reset_' . $name) && $old) {
                    Storage::disk('public')->delete($old);
                    Setting::put($key, null);
                }
                continue;
            }

            $value = $meta['type'] === 'bool' ? ($request->boolean($name) ? '1' : '0') : trim((string) $request->input($name, ''));
            if ($meta['type'] === 'textarea') {
                $value = implode("\n", array_filter(array_map('trim', preg_split('/\R/', $value)), fn ($line) => $line !== ''));
            }
            // The same as the default (or left blank) means "follow the default", so a later
            // change in Site Content still reaches this document.
            $default = DocumentLayouts::defaultValue($document, $field);
            if ($value === '' && !empty($meta['optional'])) {
                $value = DocumentLayouts::NONE; // left out of the document on purpose
            }
            Setting::put($key, $value === '' || $value === $default ? null : $value);
        }

        DocumentLayouts::flush();
        AuditLog::record('Document Layout Updated', "{$title} layout updated by " . auth()->user()->name);

        return back()->with(['success' => "{$title} layout saved. The next download uses it.", 'cms_panel' => 'panel-layouts']);
    }

    // The Word template of a Memorandum, Request Letter or Advice Slip, built from its column.
    public function template(string $document)
    {
        abort_unless(!empty(DocumentLayouts::DOCUMENTS[$document]['template']), 404);
        $word = (new \App\Services\OfficialDocumentBuilder())->build($document);
        $path = tempnam(sys_get_temp_dir(), 'layout_');
        \PhpOffice\PhpWord\IOFactory::createWriter($word, 'Word2007')->save($path);

        return response()->download($path, \App\Services\OfficialDocumentBuilder::fileName($document), [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }

    // An uploaded logo, for the preview in the column.
    public function image(string $document, string $side)
    {
        abort_unless(isset(DocumentLayouts::DOCUMENTS[$document]) && in_array($side, ['left', 'right'], true), 404);
        $path = DocumentLayouts::uploadedImage($document, 'header.logo_' . $side);
        abort_unless((bool) $path, 404);

        return Storage::disk('public')->response($path);
    }
}
