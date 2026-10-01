<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollaborativeDocumentViewer extends Model
{
    public $timestamps = false;

    protected $fillable = ['collaborative_document_id', 'user_id', 'last_opened_at'];

    protected function casts(): array
    {
        return [
            'last_opened_at' => 'datetime',
        ];
    }

    public function document()
    {
        return $this->belongsTo(CollaborativeDocument::class, 'collaborative_document_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
