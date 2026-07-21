<?php

namespace Database\Factories;

use App\Enums\SosUrgency;
use App\Models\SosServiceType;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmergencyGuidanceRequest>
 */
class EmergencyGuidanceRequestFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'vehicle_id' => Vehicle::factory(),
            'description' => fake()->sentence(),
            'symptoms_json' => [fake()->word()],
            'latitude' => fake()->latitude(-90, 90),
            'longitude' => fake()->longitude(-180, 180),
            'ai_response' => ['safe_steps' => ['Stop safely.']],
            'urgency' => SosUrgency::Medium,
            'needs_sos' => false,
            'recommended_sos_service_type_id' => SosServiceType::factory(),
            'safety_message' => fake()->sentence(),
        ];
    }
}
