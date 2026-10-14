<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

test('an editor can schedule a post with a future publication date', function () {
    $user = editorWithPermissions(['post.create', 'post.publish']);
    $category = Category::factory()->create();

    Volt::actingAs($user)
        ->test('dashboard.posts.create')
        ->set('title', 'Un article programmé')
        ->set('body', '<p>Contenu</p>')
        ->set('category_id', $category->id)
        ->set('status', 'scheduled')
        ->set('published_at', now()->addDay()->format('Y-m-d\TH:i'))
        ->call('save')
        ->assertHasNoErrors();

    $post = Post::where('slug', 'un-article-programme')->sole();

    expect($post->status)->toBe('scheduled');
    expect($post->published_at->isFuture())->toBeTrue();
});

test('a scheduled post requires a publication date in the future', function () {
    $user = editorWithPermissions(['post.create', 'post.publish']);
    $category = Category::factory()->create();

    Volt::actingAs($user)
        ->test('dashboard.posts.create')
        ->set('title', 'Un article sans date')
        ->set('body', '<p>Contenu</p>')
        ->set('category_id', $category->id)
        ->set('status', 'scheduled')
        ->call('save')
        ->assertHasErrors(['published_at' => 'required']);

    expect(Post::where('slug', 'un-article-sans-date')->exists())->toBeFalse();
});

test('a user without the publish permission cannot schedule a post', function () {
    $user = editorWithPermissions(['post.create']);
    $category = Category::factory()->create();

    Volt::actingAs($user)
        ->test('dashboard.posts.create')
        ->set('title', 'Statut forcé')
        ->set('body', '<p>Contenu</p>')
        ->set('category_id', $category->id)
        ->set('status', 'scheduled')
        ->set('published_at', now()->addDay()->format('Y-m-d\TH:i'))
        ->call('save')
        ->assertHasNoErrors();

    expect(Post::where('slug', 'statut-force')->sole()->status)->toBe('draft');
});

test('a featured image is stored when creating a post', function () {
    Storage::fake('public');

    $user = editorWithPermissions(['post.create']);
    $category = Category::factory()->create();

    Volt::actingAs($user)
        ->test('dashboard.posts.create')
        ->set('title', 'Article avec image')
        ->set('body', '<p>Contenu</p>')
        ->set('category_id', $category->id)
        ->set('featured_image', UploadedFile::fake()->image('cover.jpg'))
        ->call('save')
        ->assertHasNoErrors();

    $post = Post::where('slug', 'article-avec-image')->sole();

    expect($post->featured_image)->toStartWith('posts/');
    Storage::disk('public')->assertExists($post->featured_image);
});

test('the edit form can update the publication date of a scheduled post', function () {
    $user = editorWithPermissions(['post.edit', 'post.publish']);
    $post = Post::factory()->scheduled()->create();

    Volt::actingAs($user)
        ->test('dashboard.posts.edit', ['post' => $post])
        ->set('published_at', now()->addDays(10)->format('Y-m-d\TH:i'))
        ->call('save')
        ->assertHasNoErrors();

    expect($post->fresh()->published_at->isFuture())->toBeTrue();
});

test('the edit form can replace the featured image and deletes the previous file', function () {
    Storage::fake('public');
    Storage::disk('public')->put('posts/ancienne.jpg', 'contenu');

    $user = editorWithPermissions(['post.edit']);
    $post = Post::factory()->create(['featured_image' => 'posts/ancienne.jpg']);

    Volt::actingAs($user)
        ->test('dashboard.posts.edit', ['post' => $post])
        ->set('featured_image', UploadedFile::fake()->image('nouvelle.jpg'))
        ->call('save')
        ->assertHasNoErrors();

    $post->refresh();

    expect($post->featured_image)->toStartWith('posts/');
    Storage::disk('public')->assertExists($post->featured_image);
    Storage::disk('public')->assertMissing('posts/ancienne.jpg');
});

test('the edit form can remove the featured image', function () {
    Storage::fake('public');
    Storage::disk('public')->put('posts/ancienne.jpg', 'contenu');

    $user = editorWithPermissions(['post.edit']);
    $post = Post::factory()->create(['featured_image' => 'posts/ancienne.jpg']);

    Volt::actingAs($user)
        ->test('dashboard.posts.edit', ['post' => $post])
        ->set('featured_image', null)
        ->call('save')
        ->assertHasNoErrors();

    expect($post->fresh()->featured_image)->toBeNull();
    Storage::disk('public')->assertMissing('posts/ancienne.jpg');
});
