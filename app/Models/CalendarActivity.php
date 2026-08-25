<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarActivity extends Model
{
    protected $fillable = [
        'created_by', 'title', 'description',
        'activity_date', 'location', 'category',
    ];

    protected $casts = [
        'activity_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Human-readable category label
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'exam'    => 'Exam Period',
            'faculty' => 'Faculty Required',
            'holiday' => 'Holiday',
            default   => 'All Roles',
        };
    }
}