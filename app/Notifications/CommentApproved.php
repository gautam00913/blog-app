<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class CommentApproved extends Notification implements ShouldBroadcast
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
        return [
            'id' => $this->id,
            'type' => 'comment_approved',
            'comment_id' => $this->comment->id,
            'post_id' => $this->comment->post->id,
            'post_title' => $this->comment->post->title,
            'post_slug' => $this->comment->post->slug,
            'message' => "Votre commentaire sur « {$this->comment->post->title} » a été approuvé",
            'url' => route('blogs.show', $this->comment->post->slug),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'type' => 'comment_approved',
            'comment_id' => $this->comment->id,
            'post_id' => $this->comment->post->id,
            'post_title' => $this->comment->post->title,
            'post_slug' => $this->comment->post->slug,
            'message' => "Votre commentaire sur « {$this->comment->post->title} » a été approuvé",
            'url' => route('blogs.show', $this->comment->post->slug),
        ]);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notifications'),
        ];
    }
}
