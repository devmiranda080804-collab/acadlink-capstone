<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateCopy extends Model
{
    protected $fillable = ['template_document_id', 'faculty_id', 'title', 'google_doc_id'];

    public function templateDocument()
    {
        return $this->belongsTo(TemplateDocument::class);
    }

    public function faculty()
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }

    public function getGoogleEditUrlAttribute(): string
    {
        return "https://docs.google.com/document/d/{$this->google_doc_id}/edit";
    }
}
