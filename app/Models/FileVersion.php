<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// An earlier copy of a document, kept when a newer save replaces it.
class FileVersion extends Model
{
    // How many earlier copies each document keeps.
    const KEEP = 20;

    protected $fillable = ['file_path', 'file_name', 'file_type', 'file_size', 'edited_by', 'saved_at'];

    protected function casts(): array
    {
        return ['saved_at' => 'datetime'];
    }

    public function versionable()
    {
        return $this->morphTo();
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
}
