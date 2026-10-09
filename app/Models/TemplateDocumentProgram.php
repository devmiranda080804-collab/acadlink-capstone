<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateDocumentProgram extends Model
{
    // distributed_by / distributed_at record when the Dean sent the template to this program.
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
