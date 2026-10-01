<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RoleChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public array $oldRoles,
        public array $newRoles,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre rôle a été mis à jour sur '.config('app.name', 'Blog'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.role-changed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
