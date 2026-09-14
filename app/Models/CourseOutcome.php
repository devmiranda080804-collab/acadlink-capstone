<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseOutcome extends Model
{
    protected $fillable = ['course_id', 'code', 'description', 'sample_activities', 'order', 'created_by'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function programOutcomes()
    {
        return $this->belongsToMany(ProgramOutcome::class, 'co_po_mapping', 'course_outcome_id', 'program_outcome_id')
            ->withPivot('level')
            ->withTimestamps();
    }

    public function isMappedTo(int $programOutcomeId): bool
    {
        return $this->programOutcomes->contains('id', $programOutcomeId);
    }
}
