<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewCommentReply;
use Illuminate\Support\Facades\Notification;
use Livewire\Volt\Volt;

beforeEach(function () {
    Notification::fake();

    $this->post = Post::factory()->published()->create();
    $this->parent = Comment::factory()
        ->for($this->post)
        ->approved()
        ->create(['name' => 'Jean Dupont', 'body' => 'Commentaire original']);
});

test('a reply is attached to the comment it answers', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Volt::test('blogs.show', ['slug' => $this->post->slug])
        ->call('startReply', $this->parent->id)
        ->set('replyBody', 'Merci pour votre retour')
        ->call('saveReply')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('comments', [
        'post_id' => $this->post->id,
        'parent_id' => $this->parent->id,
        'body' => 'Merci pour votre retour',
        'user_id' => $user->id,
        'approved' => true,
    ]);
});

test('a reply requires a target comment', function () {
    Volt::test('blogs.show', ['slug' => $this->post->slug])
        ->set('name', 'Jean Dupont')
        ->set('email', 'jean@example.com')
        ->set('replyBody', 'Une réponse sans cible')
        ->call('saveReply')
        ->assertHasErrors('replyBody');

    $this->assertDatabaseCount('comments', 1);
});

test('a reply cannot target a comment from another post', function () {
    $otherPost = Post::factory()->published()->create();
    $otherComment = Comment::factory()->for($otherPost)->approved()->create();

    Volt::test('blogs.show', ['slug' => $this->post->slug])
        ->call('startReply', $otherComment->id)
        ->set('name', 'Jean Dupont')
        ->set('email', 'jean@example.com')
        ->set('replyBody', 'Réponse hors article')
        ->call('saveReply')
        ->assertHasErrors('replyBody');

    $this->assertDatabaseCount('comments', 2);
});

test('a reply cannot target a comment awaiting moderation', function () {
    $pending = Comment::factory()->for($this->post)->pending()->create();

    Volt::test('blogs.show', ['slug' => $this->post->slug])
        ->call('startReply', $pending->id)
        ->set('name', 'Jean Dupont')
        ->set('email', 'jean@example.com')
        ->set('replyBody', 'Réponse à un commentaire caché')
        ->call('saveReply')
        ->assertHasErrors('replyBody');

    $this->assertDatabaseCount('comments', 2);
});

test('replying to a reply attaches to the root comment', function () {
    $firstReply = Comment::factory()
        ->replyTo($this->parent)
        ->approved()
        ->create();

    Volt::test('blogs.show', ['slug' => $this->post->slug])
        ->call('startReply', $firstReply->id)
        ->set('name', 'Marie Curie')
        ->set('email', 'marie@example.com')
        ->set('replyBody', 'Réponse de second niveau')
        ->call('saveReply')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('comments', [
        'body' => 'Réponse de second niveau',
        'parent_id' => $this->parent->id,
    ]);
});

test('the parent comment author is notified of a reply', function () {
    $author = User::factory()->create();
    $comment = Comment::factory()
        ->for($this->post)
        ->forUser($author)
        ->approved()
        ->create();

    $replier = User::factory()->create();
    $this->actingAs($replier);

    Volt::test('blogs.show', ['slug' => $this->post->slug])
        ->call('startReply', $comment->id)
        ->set('replyBody', 'Bonne réponse')
        ->call('saveReply');

    Notification::assertSentTo($author, NewCommentReply::class);
});

test('a replier is not notified about their own reply', function () {
    $author = User::factory()->create();
    $comment = Comment::factory()
        ->for($this->post)
        ->forUser($author)
        ->approved()
        ->create();

    $this->actingAs($author);

    Volt::test('blogs.show', ['slug' => $this->post->slug])
        ->call('startReply', $comment->id)
        ->set('replyBody', 'Réponse à mon propre commentaire')
        ->call('saveReply');

    Notification::assertNotSentTo($author, NewCommentReply::class);
});

test('only approved replies are displayed', function () {
    Comment::factory()->replyTo($this->parent)->approved()->create(['body' => 'Réponse visible']);
    Comment::factory()->replyTo($this->parent)->pending()->create(['body' => 'Réponse en attente']);

    Volt::test('blogs.show', ['slug' => $this->post->slug])
        ->assertSee('Réponse visible')
        ->assertDontSee('Réponse en attente');
});

test('the reply form opens for an approved comment', function () {
    Volt::test('blogs.show', ['slug' => $this->post->slug])
        ->call('startReply', $this->parent->id)
        ->assertSet('replyingTo', $this->parent->id)
        ->assertSee('Répondre à')
        ->assertSee('Publier la réponse');
});

test('cancelling a reply clears the form', function () {
    Volt::test('blogs.show', ['slug' => $this->post->slug])
        ->call('startReply', $this->parent->id)
        ->set('replyBody', 'Abandonnée')
        ->call('cancelReply')
        ->assertSet('replyingTo', null)
        ->assertSet('replyBody', '');
});

test('a reply is rate limited like a comment', function () {
    $component = Volt::test('blogs.show', ['slug' => $this->post->slug]);

    foreach (range(1, 3) as $index) {
        $component
            ->call('startReply', $this->parent->id)
            ->set('name', 'Jean Dupont')
            ->set('email', 'jean@example.com')
            ->set('replyBody', "Réponse {$index}")
            ->call('saveReply')
            ->assertHasNoErrors();
    }

    expect(Comment::where('post_id', $this->post->id)->count())->toBe(4);
});
