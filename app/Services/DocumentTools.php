<?php

namespace App\Services;

use App\Models\FileVersion;
use App\Support\FileLinks;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

// Version history and PDF conversion, shared by the Faculty CMS and the System CMS.
class DocumentTools
{
    public function history(Model $document, string $restoreBase): array
    {
        return $document->fileVersions()->with('editor')->latest('id')->get()->map(fn (FileVersion $version) => [
            'saved_at'     => \App\Support\DisplayTime::format($version->saved_at ?? $version->created_at),
            'editor'       => $version->editor->name ?? null,
            'size'         => $this->readable($version->file_size),
            'download_url' => FileLinks::download('version', $version->id),
            'restore_url'  => url($restoreBase . '/' . $version->id . '/restore'),
        ])->all();
    }

    // Asks the editor service for a Word copy. Returns the finished file's contents, or null
    // while it's still working. The caller asks again; the same key continues the same job.
    public function convertToWord(Model $document): ?string
    {
        $url = URL::temporarySignedRoute('onlyoffice.file', now()->addHour(), [
            'source' => $document::ONLYOFFICE_SOURCE,
            'id'     => $document->id,
        ], absolute: false);

        $data = app(OnlyOffice::class)->convert([
            'async'      => true,
            'filetype'   => $document->extension(),
            'outputtype' => 'docx',
            'key'        => 'convert-' . $document::ONLYOFFICE_SOURCE . '-' . $document->id . '-' . md5($document->file_path . $document->file_size . $document->updated_at),
            'title'      => $document->file_name,
            'url'        => $url,
        ]);

        if (empty($data['endConvert'])) {
            return null;
        }

        $file = Http::timeout(60)->get($data['fileUrl']);
        abort_unless($file->successful() && $file->body() !== '', 502, 'The converted file could not be downloaded. Please try again.');

        return $file->body();
    }

    // Stores the Word copy and returns its details for a new record.
    public function storeWordCopy(string $contents, Model $document, string $folder): array
    {
        $path = $folder . '/' . bin2hex(random_bytes(20)) . '.docx';
        Storage::disk('public')->put($path, $contents);

        return [
            'file_path' => $path,
            'file_name' => pathinfo($document->file_name, PATHINFO_FILENAME) . '.docx',
            'file_type' => 'docx',
            'file_size' => strlen($contents),
        ];
    }

    protected function readable(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}
