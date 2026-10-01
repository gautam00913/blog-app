<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'user_id' => null,
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'body' => fake()->paragraph(2),
            'approved' => fake()->boolean(70),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => ['approved' => true]);
    }

    public function pending(): static
    {
        return $this->state(fn () => ['approved' => false]);
    }

    public function forUser(User $user): static
    {
        return $this->state(fn () => [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function replyTo(Comment $parent): static
    {
        return $this->state(fn () => [
            'post_id' => $parent->post_id,
            'parent_id' => $parent->id,
        ]);
    }
}
