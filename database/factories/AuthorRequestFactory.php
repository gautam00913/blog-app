<?php

namespace Database\Factories;

use App\Models\AuthorRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuthorRequest>
 */
class AuthorRequestFactory extends Factory
{
    protected $model = AuthorRequest::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'last_degree' => fake()->randomElement(['Master', 'Licence', 'Doctorat', 'DUT', 'BTS']).' '.fake()->word(),
            'cv_path' => 'author-requests/cv/'.fake()->uuid().'.pdf',
            'cv_original_name' => 'CV.pdf',
            'motivation_letter' => fake()->paragraphs(3, true),
            'status' => 'pending',
            'reviewed_at' => null,
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn () => [
            'status' => 'accepted',
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => 'rejected',
            'reviewed_at' => now(),
        ]);
    }
}
