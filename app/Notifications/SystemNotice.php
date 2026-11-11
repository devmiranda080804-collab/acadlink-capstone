<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

// One entry in the bell at the top of every page: what happened, and where to go to see it.
class SystemNotice extends Notification
{
    public function __construct(
        public string $title,
        public string $body,
        public string $path,
        public string $icon = 'bell',
        public ?string $key = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body'  => $this->body,
            'path'  => $this->path,
            'icon'  => $this->icon,
            'key'   => $this->key,
        ];
    }
}
