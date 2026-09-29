<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['code', 'title', 'program', 'year_level', 'semester_offered'];

    // Orders courses the way they appear in the curriculum: First Year First
    // Semester -> ... -> Fourth Year Second Semester, with Summer/Middle Term
    // subjects (year_level set but no regular-semester slot) sorted last within their year.
    public function scopeCurriculumOrder($query)
    {
        return $query->orderBy('year_level')
            ->orderByRaw("FIELD(semester_offered, 'First Semester', 'Second Semester', 'Summer')")
            ->orderBy('code');
    }

    public function materials()
    {
        return $this->hasMany(CourseMaterial::class);
    }

    public function collaborativeDocuments()
{
    return $this->hasMany(CollaborativeDocument::class);
}
public function assignments()
{
    return $this->hasMany(ProgramAssignment::class);
}

public function outcomes()
{
    return $this->hasMany(CourseOutcome::class)->orderBy('order');
}

public function topics()
{
    return $this->hasMany(CourseTopic::class)->orderBy('order');
}
}