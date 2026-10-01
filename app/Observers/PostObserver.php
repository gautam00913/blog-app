<?php

namespace App\Observers;

use App\Models\Post;
use App\Models\Subscriber;
use App\Notifications\NewPostPublishedToSubscribers;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

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

        $notification = new NewPostPublishedToSubscribers($post);

        Subscriber::query()
            ->verified()
            ->each(fn (Subscriber $subscriber) => $subscriber->notify($notification));
    }
}
