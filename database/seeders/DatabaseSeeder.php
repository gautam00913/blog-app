<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            AdminUserSeeder::class,
            UserSeeder::class,
            PostSeeder::class,
        ]);
    }
}
