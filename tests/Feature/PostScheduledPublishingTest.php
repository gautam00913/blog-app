<?php

use App\Models\Post;
use App\Models\Subscriber;
use App\Models\User;
use App\Notifications\NewPostPublished;
use App\Notifications\NewPostPublishedToSubscribers;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

test('the scheduled publishing command publishes due posts only', function () {
    Notification::fake();

    $due = Post::factory()->scheduled()->create(['published_at' => now()->subMinute()]);
    $future = Post::factory()->scheduled()->create(['published_at' => now()->addDay()]);
    $draft = Post::factory()->draft()->create(['published_at' => null]);

    $this->artisan('posts:publish-scheduled')->assertSuccessful();

    expect($due->fresh()->status)->toBe('published');
    expect($future->fresh()->status)->toBe('scheduled');
    expect($draft->fresh()->status)->toBe('draft');
});

test('publishing a due scheduled post notifies subscribers and editors', function () {
    Notification::fake();

    $subscriber = Subscriber::factory()->verified()->create();

    $editorRole = Role::findOrCreate('editor', 'web');
    $editor = User::factory()->create();
    $editor->assignRole($editorRole);

    Post::factory()->scheduled()->create(['published_at' => now()->subMinute()]);

    $this->artisan('posts:publish-scheduled')->assertSuccessful();

    Notification::assertSentTo($subscriber, NewPostPublishedToSubscribers::class);
    Notification::assertSentTo($editor, NewPostPublished::class);
});

test('a scheduled post that is not due yet does not notify anyone', function () {
    Notification::fake();

    Subscriber::factory()->verified()->create();

    Post::factory()->scheduled()->create(['published_at' => now()->addDay()]);

    $this->artisan('posts:publish-scheduled')->assertSuccessful();

    Notification::assertNothingSent();
});
