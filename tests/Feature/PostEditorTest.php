<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Livewire\Volt\Volt;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function editorWithPermissions(array $permissions): User
{
    $role = Role::findOrCreate('editor', 'web');
    $role->givePermissionTo(
        collect($permissions)->map(fn (string $name) => Permission::findOrCreate($name, 'web'))
    );

    $user = User::factory()->create();
    $user->assignRole($role);

    return $user;
}

test('the post form renders a rich text editor bound to the body property', function () {
    $user = editorWithPermissions(['post.create']);

    $this->actingAs($user)
        ->get(route('dashboard.posts.create'))
        ->assertOk()
        ->assertSee('<trix-editor', escape: false)
        ->assertSee("richTextEditor('body'", escape: false);
});

test('the edit form seeds the rich text editor with the stored body', function () {
    $user = editorWithPermissions(['post.edit']);

    $post = Post::factory()->published()->create([
        'body' => '<p>Contenu existant</p>',
    ]);

    $this->actingAs($user)
        ->get(route('dashboard.posts.edit', $post))
        ->assertOk()
        ->assertSee('<trix-editor', escape: false)
        ->assertSee("richTextEditor('body',", escape: false);
});

test('the editor is not available without the create permission', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.posts.create'))
        ->assertForbidden();
});

test('a post body keeps its html through a save', function () {
    $user = editorWithPermissions(['post.create']);

    $category = Category::factory()->create();

    $body = '<p>Premier paragraphe</p><div>Second paragraphe</div>';

    Volt::actingAs($user)
        ->test('dashboard.posts.create')
        ->set('title', 'Un article riche')
        ->set('excerpt', 'Un extrait')
        ->set('body', $body)
        ->set('category_id', $category->id)
        ->call('save')
        ->assertHasNoErrors();

    expect(Post::where('slug', 'un-article-riche')->sole()->body)->toBe($body);
});
