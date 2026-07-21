<?php

namespace Database\Factories;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();

        return [
            'workshop_id' => Workshop::factory()->approved(),
            'user_id' => $user->id,
            'vehicle_id' => Vehicle::factory()->create(['user_id' => $user->id])->id,
            'booking_id' => null,
            'diagnosis_id' => null,
            'sos_request_id' => null,
            'source' => LeadSource::ProfileView,
            'status' => LeadStatus::New,
            'notes' => null,
        ];
    }
}
