<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['code', 'title', 'program'];

    public function materials()
    {
        return $this->hasMany(CourseMaterial::class);
    }

    public function collaborativeDocuments()
{
    return $this->hasMany(CollaborativeDocument::class);
}
}