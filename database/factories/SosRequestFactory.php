<?php

namespace Database\Factories;

use App\Enums\SosRequestStatus;
use App\Enums\SosUrgency;
use App\Models\SosServiceType;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SosRequest>
 */
class SosRequestFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();

        return [
            'user_id' => $user->id,
            'vehicle_id' => Vehicle::factory()->create(['user_id' => $user->id])->id,
            'sos_service_type_id' => SosServiceType::factory(),
            'assigned_provider_id' => null,
            'description' => fake()->sentence(),
            'image_path' => null,
            'latitude' => 30.0444,
            'longitude' => 31.2357,
            'urgency' => SosUrgency::Medium,
            'status' => SosRequestStatus::Pending,
        ];
    }
}
