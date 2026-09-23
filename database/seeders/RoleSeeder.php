<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        Permission::create(['name' => 'post.create']);
        Permission::create(['name' => 'post.edit']);
        Permission::create(['name' => 'post.delete']);
        Permission::create(['name' => 'post.publish']);
        Permission::create(['name' => 'category.manage']);
        Permission::create(['name' => 'comment.manage']);
        Permission::create(['name' => 'user.manage']);

        // Create roles and assign permissions
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $editor = Role::create(['name' => 'editor']);
        $editor->givePermissionTo([
            'post.create',
            'post.edit',
            'post.delete',
            'post.publish',
            'comment.manage',
        ]);

        $author = Role::create(['name' => 'author']);
        $author->givePermissionTo([
            'post.create',
            'post.edit',
            'post.delete',
        ]);

        $subscriber = Role::create(['name' => 'subscriber']);
    }
}
