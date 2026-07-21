<?php

namespace Database\Factories;

use App\Enums\RecordStatus;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MaintenanceItem>
 */
class MaintenanceItemFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->sentence(),
            'default_interval_km' => fake()->optional()->numberBetween(5000, 50000),
            'default_interval_months' => fake()->optional()->numberBetween(1, 24),
            'service_category_id' => null,
            'status' => RecordStatus::Active,
        ];
    }

    public function withCategory(): static
    {
        return $this->state(fn (array $attributes): array => [
            'service_category_id' => ServiceCategory::factory(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => RecordStatus::Inactive,
        ]);
    }
}
