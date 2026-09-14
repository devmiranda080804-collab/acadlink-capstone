<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateDocument extends Model
{
    protected $fillable = [
        'created_by', 'title', 'type', 'file_path', 'file_name', 'file_type', 'file_size',
        'google_doc_id', 'forwarded_by', 'forwarded_at',
    ];

    protected function casts(): array
    {
        return [
            'forwarded_at' => 'datetime',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function forwarder()
    {
        return $this->belongsTo(User::class, 'forwarded_by');
    }

    public function programs()
    {
        return $this->hasMany(TemplateDocumentProgram::class);
    }

    public function copies()
    {
        return $this->hasMany(TemplateCopy::class);
    }

    public function isGoogleDoc(): bool
    {
        return $this->google_doc_id !== null;
    }

    public function getGoogleViewUrlAttribute(): ?string
    {
        return $this->google_doc_id
            ? "https://docs.google.com/document/d/{$this->google_doc_id}/edit"
            : null;
    }

    public function isForwarded(): bool
    {
        return $this->forwarded_at !== null;
    }

    public function programRow(string $program): ?TemplateDocumentProgram
    {
        return $this->programs->firstWhere('program', $program);
    }

    public function isDistributedTo(string $program): bool
    {
        return (bool) $this->programRow($program)?->distributed_at;
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
