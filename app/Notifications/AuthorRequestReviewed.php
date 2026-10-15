<?php

namespace App\Notifications;

use App\Models\AuthorRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuthorRequestReviewed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public AuthorRequest $authorRequest,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->authorRequest->isAccepted()) {
            return (new MailMessage)
                ->subject('Votre demande pour devenir auteur a été acceptée')
                ->greeting('Bonjour,')
                ->line('Félicitations, votre demande pour devenir auteur a été acceptée.')
                ->line('Vous pouvez désormais créer et publier vos articles depuis votre tableau de bord.')
                ->action('Accéder au tableau de bord', route('dashboard'))
                ->line('Bonne rédaction !');
        }

        return (new MailMessage)
            ->subject('Votre demande pour devenir auteur a été refusée')
            ->greeting('Bonjour,')
            ->line("Votre demande pour devenir auteur n'a pas été retenue.")
            ->line('Vous pouvez soumettre une nouvelle candidature si votre situation évolue.')
            ->action('Soumettre une nouvelle demande', route('dashboard.become-author'))
            ->line('Merci de votre intérêt.');
    }

    public function toArray(object $notifiable): array
    {
        $accepted = $this->authorRequest->isAccepted();

        return [
            'id' => $this->id,
            'type' => $accepted ? 'author_request_accepted' : 'author_request_rejected',
            'author_request_id' => $this->authorRequest->id,
            'status' => $this->authorRequest->status,
            'message' => $accepted
                ? 'Votre demande pour devenir auteur a été acceptée'
                : 'Votre demande pour devenir auteur a été refusée',
            'url' => $accepted ? route('dashboard') : route('dashboard.become-author'),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable) + ['created_at' => now()]);
    }

    public function broadcastType(): string
    {
        return 'notification.created';
    }
}
