<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepositoryDocument extends Model
{
    protected $fillable = [
        'uploaded_by', 'title', 'program', 'doc_type',
        'file_path', 'file_name', 'file_type', 'file_size',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getReadableSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}