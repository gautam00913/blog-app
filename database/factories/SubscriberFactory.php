<?php

namespace Database\Factories;

use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SubscriberFactory extends Factory
{
    protected $model = Subscriber::class;

    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'token' => Str::random(50),
            'is_verified' => false,
            'verified_at' => null,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn () => [
            'is_verified' => true,
            'verified_at' => now(),
            'token' => Str::random(32),
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn () => [
            'is_verified' => false,
            'verified_at' => null,
        ]);
    }
}
