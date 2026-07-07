<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['user_id', 'title', 'body'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function programs()
    {
        return $this->hasMany(AnnouncementProgram::class);
    }

    // Helper: kunin ang listahan ng program codes (e.g. ['FMAD', 'OFD'])
    public function getProgramListAttribute(): array
    {
        return $this->programs->pluck('program')->toArray();
    }
}