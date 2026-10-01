<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewCommentCreated;
use Illuminate\Support\Facades\Notification;
use Livewire\Volt\Volt;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function publishedPost(): Post
{
    return Post::factory()->published()->create();
}

test('guests can submit a comment', function () {
    Notification::fake();

    $post = publishedPost();

    Volt::test('blogs.show', ['slug' => $post->slug])
        ->set('name', 'Jean Dupont')
        ->set('email', 'jean@example.com')
        ->set('body', 'Excellent article !')
        ->call('saveComment')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('comments', [
        'post_id' => $post->id,
        'user_id' => null,
        'name' => 'Jean Dupont',
        'email' => 'jean@example.com',
        'body' => 'Excellent article !',
        'approved' => true,
    ]);
});

test('authenticated comments are linked to the author', function () {
    Notification::fake();

    $post = publishedPost();
    $user = User::factory()->create();

    $this->actingAs($user);

    Volt::test('blogs.show', ['slug' => $post->slug])
        ->set('body', 'Merci pour cet article')
        ->call('saveComment')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('comments', [
        'post_id' => $post->id,
        'user_id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'approved' => true,
    ]);
});

test('the comment form is prefilled for authenticated users', function () {
    $post = publishedPost();
    $user = User::factory()->create();

    $this->actingAs($user);

    Volt::test('blogs.show', ['slug' => $post->slug])
        ->assertSet('name', $user->name)
        ->assertSet('email', $user->email);
});

test('a comment requires a name, an email and a body', function () {
    Notification::fake();

    $post = publishedPost();

    Volt::test('blogs.show', ['slug' => $post->slug])
        ->set('name', '')
        ->set('email', 'not-an-email')
        ->set('body', '')
        ->call('saveComment')
        ->assertHasErrors(['name', 'email', 'body']);

    $this->assertDatabaseCount('comments', 0);
});

test('the body must be at least three characters', function () {
    Notification::fake();

    $post = publishedPost();

    Volt::test('blogs.show', ['slug' => $post->slug])
        ->set('name', 'Jean Dupont')
        ->set('email', 'jean@example.com')
        ->set('body', 'ok')
        ->call('saveComment')
        ->assertHasErrors(['body' => 'min']);

    $this->assertDatabaseCount('comments', 0);
});

test('comments are rate limited per post', function () {
    Notification::fake();

    $post = publishedPost();

    $component = Volt::test('blogs.show', ['slug' => $post->slug]);

    foreach (range(1, 3) as $index) {
        $component
            ->set('name', 'Jean Dupont')
            ->set('email', 'jean@example.com')
            ->set('body', "Commentaire {$index}")
            ->call('saveComment')
            ->assertHasNoErrors();
    }

    $component
        ->set('name', 'Jean Dupont')
        ->set('email', 'jean@example.com')
        ->set('body', 'Un commentaire de trop')
        ->call('saveComment')
        ->assertHasErrors('body');

    expect(Comment::where('post_id', $post->id)->count())->toBe(3);
});

test('moderators are notified of a new comment', function () {
    Notification::fake();

    $permission = Permission::findOrCreate('comment.manage', 'web');

    $post = publishedPost();
    $editorRole = Role::findOrCreate('editor', 'web');
    $editorRole->givePermissionTo($permission);

    $editor = User::factory()->create();
    $editor->assignRole($editorRole);

    $this->actingAs($post->author);

    Volt::test('blogs.show', ['slug' => $post->slug])
        ->set('name', 'Jean Dupont')
        ->set('email', 'jean@example.com')
        ->set('body', 'Excellent article !')
        ->call('saveComment');

    Notification::assertSentTo($editor, NewCommentCreated::class);
    Notification::assertSentTo($post->author, NewCommentCreated::class);
});

test('only approved comments are displayed on the post page', function () {
    $post = publishedPost();

    Comment::factory()->for($post)->approved()->create(['body' => 'Commentaire approuvé']);
    Comment::factory()->for($post)->pending()->create(['body' => 'Commentaire en attente']);

    Volt::test('blogs.show', ['slug' => $post->slug])
        ->assertSee('Commentaire approuvé')
        ->assertDontSee('Commentaire en attente')
        ->assertSee('(1)');
});
