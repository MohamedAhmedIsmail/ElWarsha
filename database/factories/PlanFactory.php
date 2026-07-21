<?php

namespace Database\Factories;

use App\Enums\RecordStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => $name,
            'code' => Str::slug($name),
            'price' => fake()->randomFloat(2, 0, 2000),
            'duration_days' => 30,
            'description' => fake()->sentence(),
            'features' => [],
            'is_featured' => false,
            'status' => RecordStatus::Active,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => RecordStatus::Inactive,
        ]);
    }
}
