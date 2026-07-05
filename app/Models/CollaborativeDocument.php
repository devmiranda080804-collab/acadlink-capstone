<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollaborativeDocument extends Model
{
    protected $fillable = [
        'course_id', 'created_by', 'title', 'content', 'last_edited_by',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lastEditor()
    {
        return $this->belongsTo(User::class, 'last_edited_by');
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class, 'document_id')->latest();
    }
}