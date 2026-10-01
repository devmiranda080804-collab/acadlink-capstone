<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tos extends Model
{
    protected $table = 'tos';

    protected $fillable = [
        'program_assignment_id', 'exam_id', 'grading_period',
        'total_hours', 'total_items', 'total_points', 'created_by',
    ];

    public function programAssignment()
    {
        return $this->belongsTo(ProgramAssignment::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function topics()
    {
        return $this->hasMany(TosTopic::class);
    }
}
