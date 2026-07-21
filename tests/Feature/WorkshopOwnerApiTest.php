<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\WorkshopStatus;
use App\Models\CarBrand;
use App\Models\Service;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WorkshopOwnerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_workshop_owner_can_register_workshop(): void
    {
        $customer = User::factory()->create(['role' => UserRole::Customer]);

        $this->actingAs($customer, 'sanctum')
            ->postJson('/api/workshop/register', $this->payload())
            ->assertForbidden();
    }

    public function test_workshop_owner_can_register_pending_workshop_and_update_profile(): void
    {
        $owner = User::factory()->create(['role' => UserRole::WorkshopOwner]);

        $response = $this->actingAs($owner, 'sanctum')
            ->postJson('/api/workshop/register', $this->payload(['name' => 'Owner Garage']));

        $response
            ->assertCreated()
            ->assertJsonPath('data.workshop.name', 'Owner Garage')
            ->assertJsonPath('data.workshop.status', WorkshopStatus::Pending->value);

        $this->actingAs($owner, 'sanctum')
            ->putJson('/api/workshop/profile', ['description' => 'Updated profile'])
            ->assertOk()
            ->assertJsonPath('data.workshop.description', 'Updated profile');
    }

    public function test_owner_can_sync_services_brands_and_working_hours(): void
    {
        $owner = User::factory()->create(['role' => UserRole::WorkshopOwner]);
        Workshop::factory()->create(['owner_id' => $owner->id]);
        $service = Service::factory()->create();
        $brand = CarBrand::factory()->create();

        $this->actingAs($owner, 'sanctum')
            ->putJson('/api/workshop/services', ['service_ids' => [$service->id]])
            ->assertOk()
            ->assertJsonPath('data.workshop.services.0.id', $service->id);

        $this->actingAs($owner, 'sanctum')
            ->putJson('/api/workshop/brands', ['brand_ids' => [$brand->id]])
            ->assertOk()
            ->assertJsonPath('data.workshop.brands.0.id', $brand->id);

        $this->actingAs($owner, 'sanctum')
            ->putJson('/api/workshop/working-hours', [
                'hours' => [
                    ['day_of_week' => 'monday', 'opens_at' => '09:00', 'closes_at' => '18:00', 'is_closed' => false],
                ],
            ])
            ->assertOk()
            ->assertJsonPath('data.workshop.working_hours.0.day_of_week', 'monday');
    }

    public function test_owner_can_upload_and_delete_workshop_image(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create(['role' => UserRole::WorkshopOwner]);
        Workshop::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner, 'sanctum')
            ->postJson('/api/workshop/images', [
                'images' => [UploadedFile::fake()->image('workshop.jpg')],
                'type' => 'cover',
            ]);

        $response
            ->assertCreated()
            ->assertJsonCount(1, 'data.items');

        $path = $response->json('data.items.0.image_path');
        Storage::disk('public')->assertExists($path);

        $imageId = $response->json('data.items.0.id');
        $this->actingAs($owner, 'sanctum')
            ->deleteJson("/api/workshop/images/{$imageId}")
            ->assertOk();

        Storage::disk('public')->assertMissing($path);
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return [
            ...[
                'name' => 'Garage',
                'phone' => '01000000099',
                'address' => '12 Abbas Street',
                'city' => 'Cairo',
                'area' => 'Nasr City',
                'latitude' => 30.0444,
                'longitude' => 31.2357,
                'accepts_booking' => true,
                'accepts_sos' => true,
            ],
            ...$overrides,
        ];
    }
}
