<?php

namespace App\Observers;

use App\Models\Post;
use App\Models\Subscriber;
use App\Notifications\NewPostPublishedToSubscribers;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Support\Facades\Notification;

class PostObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Post "created" event.
     */
    public function created(Post $post): void
    {
        $this->notifyVerifiedSubscribers($post);
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post): void
    {
        if (! $post->wasChanged('status')) {
            return;
        }

        $this->notifyVerifiedSubscribers($post);
    }

    private function notifyVerifiedSubscribers(Post $post): void
    {
        if (! $post->isPublished()) {
            return;
        }

        try {
            $subscribers = Subscriber::verified()->get();
            Notification::send($subscribers, new NewPostPublishedToSubscribers($post));
        } catch (\Exception $e) {
            logger('error sending notification : '.$e->getMessage());
        }

    }
}
