<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Enums\WorkshopStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SosProvider>
 */
class SosProviderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => UserRole::Provider]),
            'workshop_id' => null,
            'name' => fake()->company(),
            'phone' => fake()->numerify('010########'),
            'whatsapp' => fake()->numerify('010########'),
            'city' => 'Cairo',
            'area' => 'Nasr City',
            'latitude' => 30.0444,
            'longitude' => 31.2357,
            'is_available' => true,
            'rating_avg' => 4.5,
            'status' => WorkshopStatus::Approved,
        ];
    }
}
