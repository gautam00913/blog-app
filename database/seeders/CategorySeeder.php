<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Technologie', 'description' => 'Articles sur les dernières technologies et frameworks', 'color' => '#FF584D'],
            ['name' => 'Éducation', 'description' => 'Guides et tutoriels pour apprendre', 'color' => '#556C96'],
            ['name' => 'Design', 'description' => 'UI/UX, graphisme et créativité', 'color' => '#8B5CF6'],
            ['name' => 'Business', 'description' => 'Entrepreneuriat et stratégie', 'color' => '#0EA5E9'],
            ['name' => 'Sécurité', 'description' => 'Bonnes pratiques de sécurité informatique', 'color' => '#F59E0B'],
            ['name' => 'DevOps', 'description' => 'Infrastructure, CI/CD et opérations', 'color' => '#06B6D4'],
            ['name' => 'Lifestyle', 'description' => 'Art de vivre et productivité', 'color' => '#10B981'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'color' => $category['color'],
            ]);
        }
    }
}
