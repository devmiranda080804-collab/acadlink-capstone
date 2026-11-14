<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// One item of a dropdown list managed in the System CMS (see App\Support\CmsLists).
class CmsOption extends Model
{
    protected $fillable = ['list', 'key', 'label', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];
}
