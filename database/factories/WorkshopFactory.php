<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Enums\WorkshopStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workshop>
 */
class WorkshopFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => User::factory()->state(['role' => UserRole::WorkshopOwner]),
            'name' => fake()->company(),
            'description' => fake()->sentence(),
            'phone' => fake()->numerify('010########'),
            'whatsapp' => fake()->numerify('010########'),
            'email' => fake()->safeEmail(),
            'address' => fake()->address(),
            'city' => 'Cairo',
            'area' => 'Nasr City',
            'latitude' => 30.0444,
            'longitude' => 31.2357,
            'google_maps_url' => null,
            'accepts_booking' => true,
            'accepts_sos' => false,
            'is_verified' => false,
            'rating_avg' => 0,
            'reviews_count' => 0,
            'status' => WorkshopStatus::Pending,
            'subscription_status' => 'free',
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => WorkshopStatus::Approved,
        ]);
    }
}
