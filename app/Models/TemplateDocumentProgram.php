<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateDocumentProgram extends Model
{
    protected $fillable = ['template_document_id', 'program', 'distributed_by', 'distributed_at'];

    protected function casts(): array
    {
        return [
            'distributed_at' => 'datetime',
        ];
    }

    public function document()
    {
        return $this->belongsTo(TemplateDocument::class, 'template_document_id');
    }

    public function distributor()
    {
        return $this->belongsTo(User::class, 'distributed_by');
    }
}
