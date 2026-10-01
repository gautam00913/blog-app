<?php

use App\Models\Post;
use App\Models\Subscriber;
use App\Notifications\NewPostPublished;
use App\Notifications\NewPostPublishedToSubscribers;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\Queue;

test('the subscriber notification is queued while the in app one is not', function () {
    expect(NewPostPublishedToSubscribers::class)->toImplement(ShouldQueue::class);
    expect(NewPostPublished::class)->not->toImplement(ShouldQueue::class);
});

test('publishing a post queues the subscriber notification', function () {
    Queue::fake();

    Subscriber::factory()->verified()->create();

    Post::factory()->published()->create();

    Queue::assertPushed(
        SendQueuedNotifications::class,
        fn (SendQueuedNotifications $job) => $job->notification instanceof NewPostPublishedToSubscribers
    );
});

test('publishing a draft queues the subscriber notification', function () {
    Queue::fake();

    Subscriber::factory()->verified()->create();

    $post = Post::factory()->draft()->create();

    Queue::assertNothingPushed();

    $post->update(['status' => 'published', 'published_at' => now()]);

    Queue::assertPushed(
        SendQueuedNotifications::class,
        fn (SendQueuedNotifications $job) => $job->notification instanceof NewPostPublishedToSubscribers
    );
});

test('editing a published post queues nothing', function () {
    Queue::fake();

    Subscriber::factory()->verified()->create();

    $post = Post::factory()->published()->create();

    Queue::assertPushed(SendQueuedNotifications::class);

    Queue::fake();

    $post->update(['title' => 'Un titre modifié']);

    Queue::assertNothingPushed();
});
