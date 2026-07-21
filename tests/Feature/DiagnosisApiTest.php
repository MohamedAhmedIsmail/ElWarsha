<?php

namespace Tests\Feature;

use App\Enums\DiagnosisStatus;
use App\Models\Diagnosis;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DiagnosisApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_completed_dummy_battery_diagnosis_for_own_vehicle(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        ServiceCategory::factory()->create(['name' => 'Electricity', 'slug' => 'electricity']);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/diagnoses', [
            'vehicle_id' => $vehicle->id,
            'description' => 'Car not starting and clicking near the battery',
            'symptoms_json' => ['sound' => 'clicking'],
            'disclaimer_accepted' => true,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.diagnosis.status', DiagnosisStatus::Completed->value)
            ->assertJsonPath('data.diagnosis.ai_response.affected_category', 'Electricity')
            ->assertJsonPath('data.diagnosis.ai_response.confidence', 'medium')
            ->assertJsonPath('data.diagnosis.ai_response.safety_disclaimer', 'This is an initial AI-assisted diagnosis and is not a replacement for a professional inspection.');

        $this->assertDatabaseHas('diagnoses', [
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'status' => DiagnosisStatus::Completed->value,
        ]);
    }

    public function test_user_cannot_create_diagnosis_for_another_users_vehicle(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user, 'sanctum')->postJson('/api/diagnoses', [
            'vehicle_id' => $vehicle->id,
            'description' => 'Brake noise',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['vehicle_id']);
    }

    public function test_user_can_list_and_show_only_own_diagnoses(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $ownDiagnosis = Diagnosis::factory()->create(['user_id' => $user->id, 'vehicle_id' => Vehicle::factory()->create(['user_id' => $user->id])->id]);
        $otherDiagnosis = Diagnosis::factory()->create(['user_id' => $otherUser->id, 'vehicle_id' => Vehicle::factory()->create(['user_id' => $otherUser->id])->id]);

        $this->actingAs($user, 'sanctum')->getJson('/api/diagnoses')
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.id', $ownDiagnosis->id);

        $this->actingAs($user, 'sanctum')->getJson("/api/diagnoses/{$ownDiagnosis->id}")
            ->assertOk()
            ->assertJsonPath('data.diagnosis.id', $ownDiagnosis->id);

        $this->actingAs($user, 'sanctum')->getJson("/api/diagnoses/{$otherDiagnosis->id}")
            ->assertNotFound();
    }

    public function test_user_can_upload_diagnosis_media(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $diagnosis = Diagnosis::factory()->create(['user_id' => $user->id, 'vehicle_id' => Vehicle::factory()->create(['user_id' => $user->id])->id]);

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/diagnoses/{$diagnosis->id}/media", [
            'media_type' => 'image',
            'files' => [UploadedFile::fake()->image('fault.jpg')],
        ]);

        $response
            ->assertCreated()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.media_type', 'image');

        Storage::disk('public')->assertExists($response->json('data.items.0.file_path'));
    }

    public function test_recommended_workshops_are_stored_and_returned_for_affected_category(): void
    {
        $user = User::factory()->create();
        $category = ServiceCategory::factory()->create(['name' => 'Brakes', 'slug' => 'brakes']);
        $service = Service::factory()->create(['service_category_id' => $category->id]);
        $diagnosis = Diagnosis::factory()
            ->completed($category)
            ->create(['user_id' => $user->id, 'vehicle_id' => Vehicle::factory()->create(['user_id' => $user->id])->id]);

        $verified = Workshop::factory()->approved()->create([
            'name' => 'Verified Brake Center',
            'is_verified' => true,
            'rating_avg' => 4.8,
            'latitude' => 30.0444,
            'longitude' => 31.2357,
        ]);
        $verified->services()->sync([$service->id]);

        $pending = Workshop::factory()->create(['name' => 'Pending Brake Center']);
        $pending->services()->sync([$service->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/diagnoses/{$diagnosis->id}/recommended-workshops?lat=30.0444&lng=31.2357");

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.workshop.name', 'Verified Brake Center');

        $this->assertDatabaseHas('diagnosis_workshop_suggestions', [
            'diagnosis_id' => $diagnosis->id,
            'workshop_id' => $verified->id,
        ]);
    }
}
