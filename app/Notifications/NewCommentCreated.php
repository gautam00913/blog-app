<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewCommentCreated extends Notification
{
    public function __construct(
        public Comment $comment,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return $this->payload();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->payload());
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(): array
    {
        $authorName = $this->comment->user?->name ?? $this->comment->name;

        return [
            'id' => $this->id,
            'type' => 'new_comment_created',
            'comment_id' => $this->comment->id,
            'created_at' => $this->comment->created_at,
            'post_id' => $this->comment->post->id,
            'post_title' => $this->comment->post->title,
            'post_slug' => $this->comment->post->slug,
            'author_name' => $authorName,
            'message' => "Nouveau commentaire de {$authorName} sur « {$this->comment->post->title} »",
            'url' => route('dashboard.comments', ['id' => $this->comment->id]),
        ];
    }

    public function broadcastType() : string 
    {
        return 'notification.created';
    }
}