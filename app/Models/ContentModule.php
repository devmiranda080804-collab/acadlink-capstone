<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentModule extends Model
{
    protected $fillable = [
        'created_by', 'title', 'description', 'content',
        'google_doc_id', 'file_path', 'file_name', 'file_type', 'file_size',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Legacy — new modules no longer create Google Docs (see
    // ContentModuleController::store()), but older rows may still have one.
    public function isGoogleDoc(): bool
    {
        return $this->google_doc_id !== null;
    }

    // In-app WYSIWYG content, written and edited directly inside AcadLink.
    public function isWritten(): bool
    {
        return $this->google_doc_id === null && $this->file_path === null;
    }

    public function getGoogleEditUrlAttribute(): ?string
    {
        return $this->google_doc_id
            ? "https://docs.google.com/document/d/{$this->google_doc_id}/edit"
            : null;
    }

    public function getReadableSizeAttribute(): ?string
    {
        $bytes = $this->file_size;
        if ($bytes === null) return null;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}
