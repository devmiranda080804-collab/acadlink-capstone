<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnouncementProgram extends Model
{
    protected $fillable = ['announcement_id', 'program'];

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }
}