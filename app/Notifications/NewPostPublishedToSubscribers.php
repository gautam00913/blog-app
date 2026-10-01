<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPostPublishedToSubscribers extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Post $post,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Nouvel article : {$this->post->title}")
            ->greeting('Bonjour,')
            ->line($this->post->excerpt ?: 'Un nouvel article vient d\'être publié sur notre blog.')
            ->action('Lire l\'article', route('blogs.show', $this->post->slug))
            ->line('Merci de votre lecture !')
            ->line('Vous ne souhaitez plus recevoir nos emails ? '
                .'[Se désabonner]('.route('subscriber.unsubscribe', ['token' => $notifiable->token]).').');
    }
}
