<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Jean Dupont', 'email' => 'jean@example.com', 'role' => 'editor'],
            ['name' => 'Marie Laurent', 'email' => 'marie@example.com', 'role' => 'author'],
            ['name' => 'Pierre Martin', 'email' => 'pierre@example.com', 'role' => 'author'],
            ['name' => 'Sophie Chen', 'email' => 'sophie@example.com', 'role' => 'author'],
            ['name' => 'Lucas Bernard', 'email' => 'lucas@example.com', 'role' => 'subscriber'],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::create([
                ...$userData,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);

            $user->assignRole($role);
        }
    }
}
