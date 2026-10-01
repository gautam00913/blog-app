<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\PostView;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PostView>
 */
class PostViewFactory extends Factory
{
    protected $model = PostView::class;

    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'user_id' => null,
            'user_ip' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }

    public function forUser(User $user): static
    {
        return $this->state(fn () => ['user_id' => $user->id]);
    }
}
