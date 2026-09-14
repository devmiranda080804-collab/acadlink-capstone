<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollaborativeDocument extends Model
{
    protected $fillable = [
        'course_id', 'created_by', 'title', 'content', 'google_doc_id', 'last_edited_by',
    ];

    // So google_edit_url is always present when this model is turned into JSON
    // (e.g. the documents list), not just where it's built by hand
    protected $appends = ['google_edit_url'];

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

    public function getGoogleEditUrlAttribute(): ?string
    {
        return $this->google_doc_id
            ? "https://docs.google.com/document/d/{$this->google_doc_id}/edit"
            : null;
    }
}