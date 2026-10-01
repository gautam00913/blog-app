<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Notifications\Notification;

class NewCommentReply extends Notification
{
    public function __construct(
        public Comment $reply,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $authorName = $this->reply->user?->name ?? $this->reply->name;

        return [
            'id' => $this->id,
            'type' => 'new_comment_reply',
            'comment_id' => $this->reply->id,
            'post_id' => $this->reply->post->id,
            'post_title' => $this->reply->post->title,
            'post_slug' => $this->reply->post->slug,
            'author_name' => $authorName,
            'message' => "{$authorName} a répondu à votre commentaire sur « {$this->reply->post->title} »",
            'url' => route('blogs.show', $this->reply->post->slug).'#comment-'.$this->reply->parent_id,
        ];
    }
}
