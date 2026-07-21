<?php

namespace Database\Factories;

use App\Enums\DevicePlatform;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeviceToken>
 */
class DeviceTokenFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'token' => fake()->sha256(),
            'platform' => DevicePlatform::Android,
            'device_name' => fake()->optional()->word(),
        ];
    }
}
