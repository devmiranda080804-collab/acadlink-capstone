<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SharedResource extends Model
{
    protected $fillable = [
        'shared_by', 'program', 'title', 'description',
        'file_path', 'file_name', 'file_type', 'file_size',
    ];

    public function sharer()
    {
        return $this->belongsTo(User::class, 'shared_by');
    }

    public function getReadableSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}