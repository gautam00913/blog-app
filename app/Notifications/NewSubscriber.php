<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewSubscriber extends Notification
{
    public function __construct(
        public string $email,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->id,
            'type' => 'new_subscriber',
            'email' => $this->email,
            'message' => "Nouvel abonné : {$this->email}",
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'type' => 'new_subscriber',
            'email' => $this->email,
            'created_at' => now(),
            'message' => "Nouvel abonné : {$this->email}",
        ]);
    }

    public function broadcastType(): string
    {
        return 'notification.created';
    }
}
