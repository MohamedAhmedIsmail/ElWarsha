<?php

namespace Tests\Feature;

use App\Enums\RecordStatus;
use App\Enums\SosRequestStatus;
use App\Enums\UserRole;
use App\Models\SosProvider;
use App\Models\SosRequest;
use App\Models\SosServiceType;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SosApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_sos_service_types_return_active_only(): void
    {
        SosServiceType::factory()->create(['name' => 'Towing', 'slug' => 'towing']);
        SosServiceType::factory()->inactive()->create(['name' => 'Hidden', 'slug' => 'hidden']);

        $this->getJson('/api/sos-service-types')
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.name', 'Towing');
    }

    public function test_customer_can_create_sos_request_and_nearest_provider_is_assigned(): void
    {
        $customer = User::factory()->create();
        $providerUser = User::factory()->create(['role' => UserRole::Provider]);
        $workshop = Workshop::factory()->approved()->create();
        $serviceType = SosServiceType::factory()->create(['name' => 'Towing', 'slug' => 'towing', 'status' => RecordStatus::Active]);
        $nearProvider = SosProvider::factory()->create([
            'user_id' => $providerUser->id,
            'workshop_id' => $workshop->id,
            'latitude' => 30.0444,
            'longitude' => 31.2357,
            'phone' => '01011111111',
            'whatsapp' => '01022222222',
        ]);
        $nearProvider->serviceTypes()->sync([$serviceType->id]);

        $farProvider = SosProvider::factory()->create([
            'latitude' => 31.2001,
            'longitude' => 29.9187,
        ]);
        $farProvider->serviceTypes()->sync([$serviceType->id]);

        $response = $this->actingAs($customer, 'sanctum')->postJson('/api/sos-requests', [
            'sos_service_type_id' => $serviceType->id,
            'latitude' => 30.0444,
            'longitude' => 31.2357,
            'description' => 'Need towing now',
            'urgency' => 'high',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.sos_request.status', SosRequestStatus::Assigned->value)
            ->assertJsonPath('data.sos_request.assigned_provider.id', $nearProvider->id);

        $sosRequestId = $response->json('data.sos_request.id');
        $this->assertDatabaseHas('sos_request_logs', [
            'sos_request_id' => $sosRequestId,
            'new_status' => SosRequestStatus::Assigned->value,
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $providerUser->id,
            'type' => 'sos_assigned',
        ]);
        $this->assertDatabaseHas('whatsapp_messages', [
            'phone' => '01022222222',
            'template_name' => 'sos_request_assigned',
        ]);
        $this->assertDatabaseHas('leads', [
            'workshop_id' => $workshop->id,
            'sos_request_id' => $sosRequestId,
            'source' => 'sos',
        ]);
    }

    public function test_customer_can_only_access_own_sos_requests_and_cancel_assigned_request(): void
    {
        $customer = User::factory()->create();
        $otherCustomer = User::factory()->create();
        $provider = SosProvider::factory()->create();
        $ownRequest = SosRequest::factory()->create([
            'user_id' => $customer->id,
            'assigned_provider_id' => $provider->id,
            'status' => SosRequestStatus::Assigned,
        ]);
        $otherRequest = SosRequest::factory()->create(['user_id' => $otherCustomer->id]);

        $this->actingAs($customer, 'sanctum')->getJson('/api/sos-requests')
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.id', $ownRequest->id);

        $this->actingAs($customer, 'sanctum')->getJson("/api/sos-requests/{$otherRequest->id}")
            ->assertNotFound();

        $this->actingAs($customer, 'sanctum')->putJson("/api/sos-requests/{$ownRequest->id}/cancel")
            ->assertOk()
            ->assertJsonPath('data.sos_request.status', SosRequestStatus::Cancelled->value);
    }

    public function test_provider_can_manage_assigned_sos_flow_and_complete_creates_ledger(): void
    {
        $customer = User::factory()->create();
        $providerUser = User::factory()->create(['role' => UserRole::Provider]);
        $provider = SosProvider::factory()->create(['user_id' => $providerUser->id]);
        $serviceType = SosServiceType::factory()->create(['name' => 'Flat Tire', 'slug' => 'flat-tire']);
        $vehicle = Vehicle::factory()->create(['user_id' => $customer->id, 'mileage_km' => 88000]);
        $sosRequest = SosRequest::factory()->create([
            'user_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'sos_service_type_id' => $serviceType->id,
            'assigned_provider_id' => $provider->id,
            'status' => SosRequestStatus::Assigned,
        ]);

        $this->actingAs($providerUser, 'sanctum')->getJson('/api/provider/sos-requests')
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.id', $sosRequest->id);

        $this->actingAs($providerUser, 'sanctum')->putJson("/api/provider/sos-requests/{$sosRequest->id}/accept")
            ->assertOk()
            ->assertJsonPath('data.sos_request.status', SosRequestStatus::Accepted->value);

        $this->actingAs($providerUser, 'sanctum')->putJson("/api/provider/sos-requests/{$sosRequest->id}/on-the-way")
            ->assertOk()
            ->assertJsonPath('data.sos_request.status', SosRequestStatus::OnTheWay->value);

        $this->actingAs($providerUser, 'sanctum')->putJson("/api/provider/sos-requests/{$sosRequest->id}/arrived")
            ->assertOk()
            ->assertJsonPath('data.sos_request.status', SosRequestStatus::Arrived->value);

        $this->actingAs($providerUser, 'sanctum')->putJson("/api/provider/sos-requests/{$sosRequest->id}/complete")
            ->assertOk()
            ->assertJsonPath('data.sos_request.status', SosRequestStatus::Completed->value);

        $this->assertDatabaseHas('service_ledgers', [
            'sos_request_id' => $sosRequest->id,
            'vehicle_id' => $vehicle->id,
            'title' => 'Flat Tire',
            'mileage_km' => 88000,
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $customer->id,
            'type' => 'sos_status_updated',
        ]);
    }

    public function test_invalid_provider_sos_transition_is_rejected(): void
    {
        $providerUser = User::factory()->create(['role' => UserRole::Provider]);
        $provider = SosProvider::factory()->create(['user_id' => $providerUser->id]);
        $sosRequest = SosRequest::factory()->create([
            'assigned_provider_id' => $provider->id,
            'status' => SosRequestStatus::Assigned,
        ]);

        $this->actingAs($providerUser, 'sanctum')->putJson("/api/provider/sos-requests/{$sosRequest->id}/complete")
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }
}
