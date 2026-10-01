<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifySubscriberEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Vérification de votre adresse email')
            ->greeting('Bonjour,')
            ->line("Vous venez de renseigner votre adresse email *{$notifiable->email}* dans notre boîte aux lettres.")
            ->line('Veuillez valider la vérification de votre adresse pour commencer par être au parfum de nos prochaines publications.')
            ->action('Verifier mon mail', route('subscriber.verify-email', ['token' => $notifiable->token]))
            ->line('Si vous ne vous souvenez pas avoir effectué telle opération sur notre plateforme, veuillez ignorer ce courriel.')
            ->line('Merci !');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
