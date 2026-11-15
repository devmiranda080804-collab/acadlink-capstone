<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

// Turns a generated Word file into a PDF through the document editor service, so a
// preview shows the pages exactly as Word lays them out (text boxes in the header,
// page breaks, page numbers). The editor service fetches the file through a signed
// link that works for a few minutes; the copy is deleted right after.
class PrintPreview
{
    const FOLDER = 'print-preview';

    // The PDF's contents, or null when the service isn't set up or the conversion failed
    // (the page then draws the Word file itself).
    public function pdf(string $docxPath): ?string
    {
        if (!OnlyOffice::isConfigured() || !is_file($docxPath)) {
            return null;
        }

        $this->removeOldCopies();
        $token = Str::random(40);
        $copy = self::FOLDER . '/' . $token . '.docx';
        Storage::disk('local')->put($copy, file_get_contents($docxPath));

        try {
            $data = app(OnlyOffice::class)->convert([
                'async'      => false,
                'filetype'   => 'docx',
                'outputtype' => 'pdf',
                'key'        => 'preview-' . $token,
                'title'      => 'preview.docx',
                'url'        => URL::temporarySignedRoute('print-preview.file', now()->addMinutes(5), ['token' => $token], absolute: false),
            ]);
            if (empty($data['endConvert']) || empty($data['fileUrl'])) {
                return null;
            }

            $file = Http::timeout(60)->get($data['fileUrl']);

            return $file->successful() && str_starts_with($file->body(), '%PDF') ? $file->body() : null;
        } catch (\Throwable $e) {
            report($e);

            return null;
        } finally {
            Storage::disk('local')->delete($copy);
        }
    }

    // The copy the editor service asks for; tokens are random and the link is signed.
    public static function copyPath(string $token): ?string
    {
        $path = self::FOLDER . '/' . $token . '.docx';

        return preg_match('/^[A-Za-z0-9]{40}$/', $token) && Storage::disk('local')->exists($path)
            ? Storage::disk('local')->path($path)
            : null;
    }

    // Copies left behind by an interrupted request.
    protected function removeOldCopies(): void
    {
        foreach (Storage::disk('local')->files(self::FOLDER) as $file) {
            if (Storage::disk('local')->lastModified($file) < now()->subHour()->getTimestamp()) {
                Storage::disk('local')->delete($file);
            }
        }
    }
}
