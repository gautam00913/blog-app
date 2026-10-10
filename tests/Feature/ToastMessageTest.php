<?php

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Livewire\Volt\Volt;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function userWithPermissions(array $permissions): User
{
    $role = Role::findOrCreate('editor', 'web');
    $role->givePermissionTo(
        collect($permissions)->map(fn (string $name) => Permission::findOrCreate($name, 'web'))
    );

    $user = User::factory()->create();
    $user->assignRole($role);

    return $user;
}

test('deleting a post dispatches a flux toast without redirecting', function () {
    $user = userWithPermissions(['post.delete']);

    $post = Post::factory()->published()->create();

    Volt::actingAs($user)
        ->test('dashboard.posts.index')
        ->call('delete', $post->slug)
        ->assertHasNoErrors()
        ->assertDispatched('toast-show');

    expect(Post::where('slug', $post->slug)->exists())->toBeFalse();
});

test('rejecting a comment dispatches a flux toast without redirecting', function () {
    $user = userWithPermissions(['comment.manage']);

    $comment = Comment::factory()->create();

    Volt::actingAs($user)
        ->test('dashboard.comments.index')
        ->call('reject', $comment->id)
        ->assertHasNoErrors()
        ->assertDispatched('toast-show');

    expect($comment->fresh()->approved)->toBeFalse();
});

test('toggling a role dispatches a flux toast without redirecting', function () {
    $user = userWithPermissions(['user.manage']);

    $target = User::factory()->create();

    Volt::actingAs($user)
        ->test('dashboard.users.index')
        ->call('toggleRole', $target->id, 'editor')
        ->assertHasNoErrors()
        ->assertDispatched('toast-show');
});

test('deleting your own account dispatches a danger flux toast', function () {
    $user = userWithPermissions(['user.manage']);

    Volt::actingAs($user)
        ->test('dashboard.users.index')
        ->call('deleteUser', $user->id)
        ->assertHasNoErrors()
        ->assertDispatched('toast-show');

    expect($user->fresh())->not->toBeNull();
});

test('creating a post flashes a success message for the next page', function () {
    $user = userWithPermissions(['post.create']);

    $category = Category::factory()->create();

    Volt::actingAs($user)
        ->test('dashboard.posts.create')
        ->set('title', 'Article avec toast')
        ->set('excerpt', 'Un extrait')
        ->set('body', '<p>Contenu</p>')
        ->set('category_id', $category->id)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard.posts'))
        ->assertSessionHas('success', 'Article créé avec succès.');
});

test('editing a post flashes a success message for the next page', function () {
    $user = userWithPermissions(['post.edit']);

    $post = Post::factory()->draft()->create(['user_id' => $user->id]);

    Volt::actingAs($user)
        ->test('dashboard.posts.edit', ['post' => $post])
        ->set('published_at', now()->addDay()->format('Y-m-d H:i'))
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard.posts'))
        ->assertSessionHas('success', 'Article mis à jour avec succès.');
});

test('a success flash renders a flux toast trigger on the next full page load', function () {
    $user = User::factory()->create();
    $user->markEmailAsVerified();

    $this->actingAs($user)
        ->withSession(['success' => 'Catégorie créée avec succès.'])
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('<ui-toast', escape: false)
        ->assertSee('data-toast="Catégorie créée avec succès."', escape: false);
});

test('no toast trigger is rendered when there is no success flash', function () {
    $user = User::factory()->create();
    $user->markEmailAsVerified();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('<ui-toast', escape: false)
        ->assertDontSee('data-toast=', escape: false);
});
