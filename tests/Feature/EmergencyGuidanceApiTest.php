<?php

namespace Tests\Feature;

use App\Enums\RecordStatus;
use App\Enums\SosUrgency;
use App\Models\SosServiceType;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmergencyGuidanceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_high_risk_emergency_guidance_is_stored_and_recommends_sos(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $sosType = SosServiceType::factory()->create([
            'name' => 'Accident Support',
            'slug' => 'accident-support',
            'status' => RecordStatus::Active,
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/emergency-guidance', [
            'vehicle_id' => $vehicle->id,
            'description' => 'Smoke is coming from the engine',
            'symptoms' => ['smoke', 'overheating'],
            'latitude' => 30.0444,
            'longitude' => 31.2357,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.emergency_guidance.urgency', SosUrgency::High->value)
            ->assertJsonPath('data.emergency_guidance.needs_sos', true)
            ->assertJsonPath('data.emergency_guidance.recommended_sos_service_type.id', $sosType->id)
            ->assertJsonPath('data.emergency_guidance.location.latitude', 30.0444)
            ->assertJsonPath('data.emergency_guidance.location.longitude', 31.2357)
            ->assertJsonFragment(['Do not attempt roadside mechanical repairs.']);

        $this->assertDatabaseHas('emergency_guidance_requests', [
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'urgency' => SosUrgency::High->value,
            'needs_sos' => true,
            'recommended_sos_service_type_id' => $sosType->id,
        ]);
    }

    public function test_emergency_guidance_rejects_vehicle_owned_by_another_user(): void
    {
        $user = User::factory()->create();
        $otherVehicle = Vehicle::factory()->create();

        $this->actingAs($user, 'sanctum')->postJson('/api/emergency-guidance', [
            'vehicle_id' => $otherVehicle->id,
            'description' => 'Smoke is coming from the engine',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['vehicle_id']);
    }

    public function test_lower_risk_guidance_stays_safety_first_without_sos_requirement(): void
    {
        $user = User::factory()->create();
        SosServiceType::factory()->create([
            'name' => 'Towing',
            'slug' => 'towing',
            'status' => RecordStatus::Active,
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/emergency-guidance', [
            'description' => 'There is a strange noise when driving slowly',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.emergency_guidance.urgency', SosUrgency::Medium->value)
            ->assertJsonPath('data.emergency_guidance.needs_sos', false)
            ->assertJsonMissing(['replace the battery'])
            ->assertJsonFragment(['Do not attempt roadside mechanical repairs.']);
    }
}
