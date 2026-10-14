<?php

namespace App\Notifications;

use App\Models\AuthorRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAuthorRequest extends Notification implements ShouldQueue
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
        $applicant = $this->authorRequest->user;

        return (new MailMessage)
            ->subject('Nouvelle demande pour devenir auteur')
            ->greeting('Bonjour,')
            ->line("{$applicant->name} ({$applicant->email}) souhaite devenir auteur.")
            ->line("Dernier diplôme : {$this->authorRequest->last_degree}")
            ->action('Consulter la demande', route('dashboard.author-requests'))
            ->line('Connectez-vous au tableau de bord pour consulter le CV et la lettre de motivation.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->id,
            'type' => 'new_author_request',
            'author_request_id' => $this->authorRequest->id,
            'applicant_name' => $this->authorRequest->user->name,
            'applicant_email' => $this->authorRequest->user->email,
            'last_degree' => $this->authorRequest->last_degree,
            'message' => "Nouvelle demande auteur : {$this->authorRequest->user->name}",
            'url' => route('dashboard.author-requests'),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'type' => 'new_author_request',
            'author_request_id' => $this->authorRequest->id,
            'applicant_name' => $this->authorRequest->user->name,
            'created_at' => now(),
            'message' => "Nouvelle demande auteur : {$this->authorRequest->user->name}",
            'url' => route('dashboard.author-requests'),
        ]);
    }

    public function broadcastType(): string
    {
        return 'notification.created';
    }
}
