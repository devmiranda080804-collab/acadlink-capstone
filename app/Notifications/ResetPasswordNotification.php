<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Password Reset Request - CBMA System')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We received a request to reset the password for your CBMA account.')
            ->action('Reset Password', $this->url)
            ->line('If you did not request this, you can safely ignore this email.')
            ->line('This link is valid for 60 minutes only.');
    }
}