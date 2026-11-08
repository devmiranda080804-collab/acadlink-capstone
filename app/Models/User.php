<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
        protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'google_email',
        'password',
        'role',
        'program',
        'academic_year',
        'is_active',
        'must_change_password',
        'archived_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function getInitialsAttribute(): string
{
    if ($this->first_name || $this->last_name) {
        $initials = mb_strtoupper(mb_substr($this->first_name ?? '', 0, 1))
            . mb_strtoupper(mb_substr($this->last_name ?? '', 0, 1));
        return $initials ?: '?';
    }

    $parts = preg_split('/\s+/', trim($this->name));
    $initials = '';
    foreach (array_slice($parts, 0, 2) as $p) {
        $initials .= mb_strtoupper(mb_substr($p, 0, 1));
    }
    return $initials ?: '?';
}

public function sendPasswordResetNotification($token)
{
    $url = url(route('password.reset', [
        'token' => $token,
        'email' => $this->email,
    ], false));

    $this->notify(new \App\Notifications\ResetPasswordNotification($url));
}

    public function programRows()
    {
        return $this->hasMany(UserProgram::class);
    }

    // Every program the account belongs to. Faculty can teach in several; a Program Head
    // leads one. Accounts saved before user_programs existed fall back to `program`.
    public function programCodes(): array
    {
        $codes = $this->exists ? $this->programRows->pluck('program')->all() : [];
        if (!$codes && $this->program) {
            $codes = [$this->program];
        }

        return array_values(array_unique($codes));
    }

    public function belongsToProgram(?string $code): bool
    {
        return $code !== null && $code !== '' && in_array($code, $this->programCodes(), true);
    }

    public function getProgramsLabelAttribute(): string
    {
        return implode(', ', $this->programCodes());
    }

    public function scopeInProgram($query, string|array $codes)
    {
        $codes = array_values((array) $codes);

        return $query->where(fn ($q) => $q->whereIn('program', $codes)
            ->orWhereHas('programRows', fn ($p) => $p->whereIn('program', $codes)));
    }

    // Replaces the account's programs; the first is also kept in `program`. Returns the
    // change as [from, to] labels, or null when nothing changed.
    public function syncPrograms(array $codes): ?array
    {
        $before = $this->programCodes();
        $codes = array_values(array_unique(array_filter($codes, fn ($c) => is_string($c) && $c !== '')));

        $this->programRows()->whereNotIn('program', $codes)->delete();
        foreach ($codes as $code) {
            $this->programRows()->firstOrCreate(['program' => $code]);
        }
        $this->forceFill(['program' => $codes[0] ?? null])->saveQuietly();
        $this->unsetRelation('programRows');

        $sorted = fn ($list) => collect($list)->sort()->values()->all();

        return $sorted($before) === $sorted($codes) ? null : [implode(', ', $before), implode(', ', $codes)];
    }
}
