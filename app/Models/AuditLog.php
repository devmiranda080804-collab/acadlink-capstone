<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'action', 'description'];
    protected $casts = ['created_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Static helper — tawagin: AuditLog::record('Account Created', 'Created faculty account for Juan Dela Cruz');
    public static function record(string $action, string $description): void
    {
        static::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'description' => $description,
            'created_at'  => now(),
        ]);
    }
}