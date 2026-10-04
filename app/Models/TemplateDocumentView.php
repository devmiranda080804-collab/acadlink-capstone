<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateDocumentView extends Model
{
    public $timestamps = false;

    protected $fillable = ['template_document_id', 'user_id', 'viewed_at'];

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
        ];
    }
}
