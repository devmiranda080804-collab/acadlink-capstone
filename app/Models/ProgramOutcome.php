<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramOutcome extends Model
{
    protected $fillable = ['program', 'code', 'description', 'order', 'created_by'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function courseOutcomes()
    {
        return $this->belongsToMany(CourseOutcome::class, 'co_po_mapping', 'program_outcome_id', 'course_outcome_id')
            ->withPivot('level')
            ->withTimestamps();
    }
}
