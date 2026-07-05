<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentVersion extends Model
{
    public $timestamps = false;

    protected $fillable = ['document_id', 'edited_by', 'content', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];

    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
}