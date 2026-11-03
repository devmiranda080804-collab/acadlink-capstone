<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// A faculty member a CMS document is shared with, and whether they can change it.
class ContentModuleShare extends Model
{
    protected $fillable = ['content_module_id', 'user_id', 'can_edit'];

    protected function casts(): array
    {
        return ['can_edit' => 'boolean'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
