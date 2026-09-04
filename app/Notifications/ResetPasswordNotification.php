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
            ->line('Nakatanggap ka ng request para i-reset ang password ng iyong CBMA account.')
            ->action('Reset Password', $this->url)
            ->line('Kung hindi ikaw ang humiling nito, huwag na lang pansinin ang email na ito.')
            ->line('Ang link na ito ay valid lang sa loob ng 60 minuto.');
    }
}