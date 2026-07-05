<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = [
        'faculty_id', 'program', 'title', 'type',
        'file_path', 'file_name', 'file_type', 'file_size',
        'status', 'review_note', 'reviewed_by', 'approved_by',
        'distributed_at', 'distributed_by',
    ];
    protected $casts = [
        'distributed_at' => 'datetime',
    ];

    public function distributor()
    {
        return $this->belongsTo(User::class, 'distributed_by');
    }

    public function faculty()
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getReadableSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    // Human-readable status label
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending_review'   => 'Pending Review',
            'needs_revision'   => 'Needs Revision',
            'pending_approval' => 'Pending Approval',
            'approved'         => 'Approved',
            'rejected'         => 'Rejected',
            default            => ucfirst($this->status),
        };
    }
}