<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewPostPublished extends Notification
{
    public function __construct(
        public Post $post,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->id,
            'type' => 'new_post_published',
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'post_slug' => $this->post->slug,
            'author_name' => $this->post->author->name,
            'message' => "Nouvel article publié : « {$this->post->title} »",
            'url' => route('blogs.show', $this->post->slug),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'type' => 'new_post_published',
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'created_at' => $this->post->published_at,
            'post_slug' => $this->post->slug,
            'author_name' => $this->post->author->name,
            'message' => "Nouvel article publié : « {$this->post->title} »",
            'url' => route('blogs.show', $this->post->slug),
        ]);
    }

    public function broadcastType(): string
    {
        return 'notification.created';
    }
}
