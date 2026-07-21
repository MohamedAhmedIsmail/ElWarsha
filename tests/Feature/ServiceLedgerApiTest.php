<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\ServiceLedgerMediaType;
use App\Enums\SosRequestStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Diagnosis;
use App\Models\MaintenanceItem;
use App\Models\ServiceLedger;
use App\Models\SosProvider;
use App\Models\SosRequest;
use App\Models\SosServiceType;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ServiceLedgerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_and_list_manual_service_ledger_entry(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $workshop = Workshop::factory()->approved()->create();
        $item = MaintenanceItem::factory()->create(['name' => 'Oil Change']);

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/vehicles/{$vehicle->id}/service-ledger", [
            'workshop_id' => $workshop->id,
            'maintenance_item_id' => $item->id,
            'title' => 'Oil change at 50k',
            'description' => 'Routine service.',
            'service_date' => now()->toDateString(),
            'cost' => 750,
            'mileage_km' => 50000,
            'invoice_file' => UploadedFile::fake()->create('invoice.pdf', 100, 'application/pdf'),
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.service_ledger.vehicle_id', $vehicle->id)
            ->assertJsonPath('data.service_ledger.workshop_id', $workshop->id)
            ->assertJsonPath('data.service_ledger.maintenance_item.name', 'Oil Change');

        $ledgerId = $response->json('data.service_ledger.id');
        $invoicePath = $response->json('data.service_ledger.invoice_file');
        Storage::disk('public')->assertExists($invoicePath);

        $this->actingAs($user, 'sanctum')->getJson("/api/vehicles/{$vehicle->id}/service-ledger")
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.id', $ledgerId);
    }

    public function test_user_can_show_update_delete_own_service_ledger_entry(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $ledger = ServiceLedger::factory()->create([
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'title' => 'Old title',
        ]);

        $this->actingAs($user, 'sanctum')->getJson("/api/service-ledger/{$ledger->id}")
            ->assertOk()
            ->assertJsonPath('data.service_ledger.title', 'Old title');

        $this->actingAs($user, 'sanctum')->putJson("/api/service-ledger/{$ledger->id}", [
            'title' => 'Updated service',
            'description' => 'Updated notes.',
            'service_date' => now()->subDay()->toDateString(),
            'cost' => 1250,
            'mileage_km' => 51000,
        ])
            ->assertOk()
            ->assertJsonPath('data.service_ledger.title', 'Updated service')
            ->assertJsonPath('data.service_ledger.cost', '1250.00');

        $this->actingAs($user, 'sanctum')->deleteJson("/api/service-ledger/{$ledger->id}")
            ->assertOk();

        $this->assertSoftDeleted('service_ledgers', ['id' => $ledger->id]);
    }

    public function test_user_cannot_access_another_users_service_ledger_or_vehicle_history(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherVehicle = Vehicle::factory()->create(['user_id' => $otherUser->id]);
        $otherLedger = ServiceLedger::factory()->create([
            'user_id' => $otherUser->id,
            'vehicle_id' => $otherVehicle->id,
        ]);

        $this->actingAs($user, 'sanctum')->getJson("/api/vehicles/{$otherVehicle->id}/service-ledger")
            ->assertNotFound();

        $this->actingAs($user, 'sanctum')->getJson("/api/service-ledger/{$otherLedger->id}")
            ->assertNotFound();
    }

    public function test_linked_booking_diagnosis_and_sos_must_belong_to_vehicle_and_user(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $otherVehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'vehicle_id' => $otherVehicle->id,
        ]);
        $diagnosis = Diagnosis::factory()->create([
            'user_id' => $user->id,
            'vehicle_id' => $otherVehicle->id,
        ]);
        $sosRequest = SosRequest::factory()->create([
            'user_id' => $user->id,
            'vehicle_id' => $otherVehicle->id,
        ]);

        $payload = [
            'title' => 'Bad link',
            'service_date' => now()->toDateString(),
        ];

        $this->actingAs($user, 'sanctum')->postJson("/api/vehicles/{$vehicle->id}/service-ledger", [
            ...$payload,
            'booking_id' => $booking->id,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['booking_id']);

        $this->actingAs($user, 'sanctum')->postJson("/api/vehicles/{$vehicle->id}/service-ledger", [
            ...$payload,
            'diagnosis_id' => $diagnosis->id,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['diagnosis_id']);

        $this->actingAs($user, 'sanctum')->postJson("/api/vehicles/{$vehicle->id}/service-ledger", [
            ...$payload,
            'sos_request_id' => $sosRequest->id,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['sos_request_id']);
    }

    public function test_user_can_upload_service_ledger_media(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $ledger = ServiceLedger::factory()->create([
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/service-ledger/{$ledger->id}/media", [
            'media_type' => ServiceLedgerMediaType::Image->value,
            'files' => [
                UploadedFile::fake()->image('before.jpg'),
                UploadedFile::fake()->image('after.jpg'),
            ],
        ]);

        $response
            ->assertCreated()
            ->assertJsonCount(2, 'data.items')
            ->assertJsonPath('data.items.0.media_type', ServiceLedgerMediaType::Image->value);

        foreach ($response->json('data.items') as $item) {
            Storage::disk('public')->assertExists($item['file_path']);
        }
    }

    public function test_completed_booking_and_sos_ledgers_are_visible_in_vehicle_history(): void
    {
        $customer = User::factory()->create();
        $owner = User::factory()->create(['role' => UserRole::WorkshopOwner]);
        $providerUser = User::factory()->create(['role' => UserRole::Provider]);
        $workshop = Workshop::factory()->approved()->create(['owner_id' => $owner->id]);
        $vehicle = Vehicle::factory()->create(['user_id' => $customer->id]);
        $booking = Booking::factory()->create([
            'user_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'status' => BookingStatus::InProgress,
        ]);
        $provider = SosProvider::factory()->create([
            'user_id' => $providerUser->id,
            'workshop_id' => $workshop->id,
        ]);
        $sosType = SosServiceType::factory()->create(['name' => 'Towing', 'slug' => 'towing']);
        $sosRequest = SosRequest::factory()->create([
            'user_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'sos_service_type_id' => $sosType->id,
            'assigned_provider_id' => $provider->id,
            'status' => SosRequestStatus::Arrived,
        ]);

        $this->actingAs($owner, 'sanctum')->putJson("/api/workshop/bookings/{$booking->id}/complete")
            ->assertOk();
        $this->actingAs($providerUser, 'sanctum')->putJson("/api/provider/sos-requests/{$sosRequest->id}/complete")
            ->assertOk();

        $this->actingAs($customer, 'sanctum')->getJson("/api/vehicles/{$vehicle->id}/service-ledger")
            ->assertOk()
            ->assertJsonCount(2, 'data.items');
    }
}
