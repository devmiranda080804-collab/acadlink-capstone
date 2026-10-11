<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// Confirmation sent to the account owner whenever their account changes.
class AccountUpdated extends Mailable
{
    use Queueable, SerializesModels;

    const SUBJECTS = [
        'details'          => 'Your CBMA System account was updated',
        'archived'         => 'Your CBMA System account was archived',
        'restored'         => 'Your CBMA System account was restored',
        'password_changed' => 'Your CBMA System password was changed',
        'password_reset'   => 'Your CBMA System password was reset',
    ];

    /**
     * @param array<int, array{field: string, from: string, to: string}> $changes
     */
    public function __construct(
        public User $user,
        public string $event,
        public array $changes = [],
        public ?User $actor = null,
    ) {}

    public function build()
    {
        return $this->subject(self::SUBJECTS[$this->event])
            ->markdown('emails.account-updated', [
                'name'      => $this->user->name,
                'event'     => $this->event,
                'changes'   => $this->changes,
                'actorName' => $this->actor && $this->actor->id !== $this->user->id
                    ? $this->actor->name . ' (' . ucwords(str_replace('_', ' ', $this->actor->role)) . ')'
                    : null,
                'when'      => now()->format('F j, Y g:i A'),
                'loginUrl'  => url('/login'),
                'resetUrl'  => url('/forgot-password'),
            ]);
    }
}
