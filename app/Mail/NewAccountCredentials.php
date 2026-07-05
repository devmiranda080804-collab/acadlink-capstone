<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewAccountCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $temporaryPassword,
    ) {}

    public function build()
    {
        return $this->subject('Your CBMA System Account')
            ->markdown('emails.new-account', [
                'name'     => $this->user->name,
                'email'    => $this->user->email,
                'password' => $this->temporaryPassword,
                'role'     => ucwords(str_replace('_', ' ', $this->user->role)),
                'loginUrl' => url('/login'),
            ]);
    }
}