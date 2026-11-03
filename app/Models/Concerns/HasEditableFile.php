<?php

namespace App\Models\Concerns;

use App\Models\FileVersion;
use App\Models\User;
use App\Services\OnlyOffice;
use Illuminate\Support\Facades\Storage;

// A record whose file can open in the document editor (see App\Services\OnlyOffice).
trait HasEditableFile
{
    protected static function bootHasEditableFile(): void
    {
        static::deleted(function ($document) {
            foreach ($document->fileVersions as $version) {
                Storage::disk('public')->delete($version->file_path);
                $version->delete();
            }
        });
    }

    public function lastEditor()
    {
        return $this->belongsTo(User::class, 'last_edited_by');
    }

    public function fileVersions()
    {
        return $this->morphMany(FileVersion::class, 'versionable');
    }

    public function extension(): string
    {
        return strtolower((string) $this->file_type);
    }

    public function isEditableFile(): bool
    {
        return $this->file_path !== null && in_array($this->extension(), ['docx', 'xlsx', 'pptx'], true);
    }

    public function canBeConverted(): bool
    {
        return $this->file_path !== null && in_array($this->extension(), ['pdf', 'doc'], true);
    }

    abstract public function canEdit(User $user): bool;

    // Whether a save from the editor may still be written to this record.
    public function acceptsEditorSave(): bool
    {
        return $this->file_path !== null;
    }

    public function keepsVersionHistory(): bool
    {
        return true;
    }

    // Copies the current file aside before something replaces it.
    public function keepCurrentVersion(): void
    {
        if (!$this->keepsVersionHistory()) {
            return;
        }

        $path = 'file-versions/' . bin2hex(random_bytes(20)) . '.' . $this->extension();
        Storage::disk('public')->copy($this->file_path, $path);

        $this->fileVersions()->create([
            'file_path' => $path,
            'file_name' => $this->file_name,
            'file_type' => $this->file_type,
            'file_size' => Storage::disk('public')->size($path),
            'edited_by' => $this->last_edited_by,
            'saved_at'  => $this->last_edited_at ?? $this->created_at,
        ]);

        foreach ($this->fileVersions()->latest('id')->skip(FileVersion::KEEP)->take(PHP_INT_MAX)->get() as $old) {
            Storage::disk('public')->delete($old->file_path);
            $old->delete();
        }
    }

    // Makes an earlier version current again. The file being replaced stays in the history.
    public function restoreVersion(FileVersion $version, User $user): void
    {
        abort_unless($version->versionable_id === $this->id && $version->versionable_type === $this->getMorphClass(), 404);

        $this->keepCurrentVersion();
        Storage::disk('public')->put($this->file_path, Storage::disk('public')->get($version->file_path));

        $this->forceFill([
            'file_size'      => $version->file_size,
            'last_edited_by' => $user->id,
            'last_edited_at' => now(),
            'document_key'   => OnlyOffice::newKey($this), // the editor must not reopen its cached copy
        ])->save();
    }
}
