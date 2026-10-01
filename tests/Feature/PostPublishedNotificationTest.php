<?php

use App\Models\Post;
use App\Models\Subscriber;
use App\Models\User;
use App\Notifications\NewPostPublished;
use App\Notifications\NewPostPublishedToSubscribers;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();

    $this->verifiedSubscriber = Subscriber::factory()->verified()->create();
    $this->unverifiedSubscriber = Subscriber::factory()->unverified()->create();
});

test('publishing a post notifies verified subscribers only', function () {
    Post::factory()->published()->create();

    Notification::assertSentTo($this->verifiedSubscriber, NewPostPublishedToSubscribers::class);
    Notification::assertNotSentTo($this->unverifiedSubscriber, NewPostPublishedToSubscribers::class);
});

test('creating a draft post notifies nobody', function () {
    Post::factory()->draft()->create();

    Notification::assertNothingSent();
});

test('a post without a publication date is not published', function () {
    Post::factory()->create(['status' => 'published', 'published_at' => null]);

    Notification::assertNothingSent();
});

test('publishing a draft notifies verified subscribers', function () {
    $post = Post::factory()->draft()->create();

    Notification::assertNothingSent();

    $post->update(['status' => 'published', 'published_at' => now()]);

    Notification::assertSentTo($this->verifiedSubscriber, NewPostPublishedToSubscribers::class);
});

test('unpublishing a post notifies nobody', function () {
    $post = Post::factory()->published()->create();

    Notification::fake();

    $post->update(['status' => 'draft']);

    Notification::assertNothingSent();
});

test('editing an already published post does not notify subscribers again', function () {
    $post = Post::factory()->published()->create();

    Notification::fake();

    $post->update(['title' => 'Un titre modifié', 'views_count' => 4242]);

    Notification::assertNothingSent();
});

test('counting a view does not notify subscribers', function () {
    $post = Post::factory()->published()->create();

    Notification::fake();

    $this->get(route('blogs.show', $post->slug))->assertOk();

    Notification::assertNothingSent();
});

test('subscribers are notified by mail only', function () {
    Post::factory()->published()->create();

    Notification::assertSentTo(
        $this->verifiedSubscriber,
        NewPostPublishedToSubscribers::class,
        fn (NewPostPublishedToSubscribers $notification, array $channels) => $notification->via($this->verifiedSubscriber) === ['mail']
    );

    $this->assertDatabaseMissing('notifications', [
        'notifiable_type' => Subscriber::class,
        'notifiable_id' => $this->verifiedSubscriber->id,
    ]);
});

test('subscribers never receive the in app notification', function () {
    Post::factory()->published()->create();

    Notification::assertNotSentTo($this->verifiedSubscriber, NewPostPublished::class);
});

test('the notification email links to the post and to the unsubscribe page', function () {
    $post = Post::factory()->published()->create([
        'title' => 'Les observables en pratique',
        'slug' => 'les-observables-en-pratique',
    ]);

    $message = (new NewPostPublishedToSubscribers($post))->toMail($this->verifiedSubscriber);
    $mail = $message->render();

    expect($message->subject)->toBe('Nouvel article : Les observables en pratique');

    $this->assertStringContainsString($post->excerpt, $mail);
    $this->assertStringContainsString(route('blogs.show', 'les-observables-en-pratique'), $mail);
    $this->assertStringContainsString(
        route('subscriber.unsubscribe', ['token' => $this->verifiedSubscriber->token]),
        $mail
    );
});

test('the unsubscribe link in the email removes the subscription', function () {
    $post = Post::factory()->published()->create();

    $mail = (new NewPostPublishedToSubscribers($post))->toMail($this->verifiedSubscriber)->render();

    $this->assertStringContainsString(
        route('subscriber.unsubscribe', ['token' => $this->verifiedSubscriber->token]),
        $mail
    );

    $this->get(route('subscriber.unsubscribe', $this->verifiedSubscriber->token))->assertOk();

    $this->assertDatabaseMissing('subscribers', ['id' => $this->verifiedSubscriber->id]);
});

test('editors still receive the in app notification', function () {
    $editor = User::factory()->create();

    $notification = new NewPostPublished(Post::factory()->published()->create());

    $editor->notify($notification);

    Notification::assertSentTo(
        $editor,
        NewPostPublished::class,
        fn (NewPostPublished $sent, array $channels) => $sent->via($editor) === ['database', 'broadcast']
    );
});
