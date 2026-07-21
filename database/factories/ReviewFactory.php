<?php

namespace Database\Factories;

use App\Enums\ReviewStatus;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'workshop_id' => Workshop::factory()->approved(),
            'rating' => fake()->numberBetween(1, 5),
            'quality_rating' => fake()->numberBetween(1, 5),
            'price_rating' => fake()->numberBetween(1, 5),
            'punctuality_rating' => fake()->numberBetween(1, 5),
            'behavior_rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->sentence(),
            'status' => ReviewStatus::Published,
        ];
    }

    public function hidden(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ReviewStatus::Hidden,
        ]);
    }
}
