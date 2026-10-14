<?php

use App\Models\User;
use Livewire\Volt\Volt;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function permissionsAdminUser(): User
{
    $role = Role::findOrCreate('admin', 'web');
    $role->givePermissionTo(Permission::findOrCreate('user.manage', 'web'));

    $user = User::factory()->create();
    $user->assignRole($role);

    return $user;
}

function seedPermission(string $name): Permission
{
    return Permission::findOrCreate($name, 'web');
}

function seedAllPermissions(): void
{
    collect(['post.create', 'post.edit', 'post.delete', 'post.publish', 'category.manage', 'comment.manage', 'user.manage'])
        ->each(fn (string $name) => seedPermission($name));
}

test('users without the user.manage permission cannot access the permissions page', function () {
    $user = User::factory()->create();
    $user->markEmailAsVerified();

    $this->actingAs($user)
        ->get(route('dashboard.permissions'))
        ->assertForbidden();
});

test('an admin can view the permissions page with the users table', function () {
    seedAllPermissions();
    $admin = permissionsAdminUser();
    $admin->markEmailAsVerified();

    $this->actingAs($admin)
        ->get(route('dashboard.permissions'))
        ->assertOk()
        ->assertSee('Permissions')
        ->assertSee('Créer des articles');
});

test('an admin can grant a permission directly to a user', function () {
    seedPermission('post.create');
    $admin = permissionsAdminUser();
    $target = User::factory()->create();

    Volt::actingAs($admin)
        ->test('dashboard.permissions.index')
        ->call('togglePermission', $target->id, 'post.create');

    expect($target->fresh()->hasDirectPermission('post.create'))->toBeTrue();
});

test('an admin can revoke a direct permission from a user', function () {
    seedPermission('post.create');
    $admin = permissionsAdminUser();
    $target = User::factory()->create();
    $target->givePermissionTo('post.create');

    Volt::actingAs($admin)
        ->test('dashboard.permissions.index')
        ->call('togglePermission', $target->id, 'post.create');

    expect($target->fresh()->hasDirectPermission('post.create'))->toBeFalse();
});

test('an admin cannot revoke their own user.manage permission', function () {
    $admin = permissionsAdminUser();

    Volt::actingAs($admin)
        ->test('dashboard.permissions.index')
        ->call('togglePermission', $admin->id, 'user.manage');

    expect($admin->fresh()->hasDirectPermission('user.manage'))->toBeFalse();
});

test('toggling an unknown permission is ignored', function () {
    $admin = permissionsAdminUser();
    $target = User::factory()->create();

    Volt::actingAs($admin)
        ->test('dashboard.permissions.index')
        ->call('togglePermission', $target->id, 'permission.inconnue');

    expect(Permission::where('name', 'permission.inconnue')->exists())->toBeFalse();
    expect($target->fresh()->getDirectPermissions())->toBeEmpty();
});
