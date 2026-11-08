<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// One program an account belongs to (see User::programCodes()).
class UserProgram extends Model
{
    protected $fillable = ['user_id', 'program'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
