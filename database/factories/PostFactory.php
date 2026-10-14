<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->paragraph(),
            'body' => $this->generateBody(),
            'featured_image' => fake()->imageUrl(1200, 630, 'technology', true),
            'status' => fake()->randomElement(['draft', 'published', 'published', 'published']),
            'published_at' => fake()->optional(0.8)->dateTimeBetween('-6 months', 'now'),
            'views_count' => fake()->numberBetween(0, 10000),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => 'published',
            'published_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => [
            'status' => 'scheduled',
            'published_at' => fake()->dateTimeBetween('+1 day', '+1 month'),
        ]);
    }

    private function generateBody(): string
    {
        $paragraphs = [];
        $count = fake()->numberBetween(4, 8);

        for ($i = 0; $i < $count; $i++) {
            $paragraphs[] = '<p>'.fake()->paragraph(3).'</p>';
        }

        return implode("\n\n", $paragraphs);
    }
}
