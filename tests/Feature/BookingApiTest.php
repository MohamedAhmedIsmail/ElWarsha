<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_create_pending_booking_with_side_effect_records(): void
    {
        $customer = User::factory()->create();
        $owner = User::factory()->create(['role' => UserRole::WorkshopOwner]);
        $vehicle = Vehicle::factory()->create(['user_id' => $customer->id]);
        $workshop = Workshop::factory()->approved()->create([
            'owner_id' => $owner->id,
            'accepts_booking' => true,
            'phone' => '01011111111',
            'whatsapp' => '01022222222',
        ]);
        $service = Service::factory()->create();
        $workshop->services()->sync([$service->id]);

        $response = $this->actingAs($customer, 'sanctum')->postJson('/api/bookings', [
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'service_id' => $service->id,
            'scheduled_at' => now()->addDay()->toISOString(),
            'description' => 'Need inspection',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.booking.status', BookingStatus::Pending->value)
            ->assertJsonPath('data.booking.workshop.id', $workshop->id);

        $bookingId = $response->json('data.booking.id');
        $this->assertDatabaseHas('booking_status_logs', [
            'booking_id' => $bookingId,
            'new_status' => BookingStatus::Pending->value,
        ]);
        $this->assertDatabaseHas('leads', [
            'booking_id' => $bookingId,
            'workshop_id' => $workshop->id,
            'source' => 'booking',
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $owner->id,
            'type' => 'booking_created',
        ]);
        $this->assertDatabaseHas('whatsapp_messages', [
            'workshop_id' => $workshop->id,
            'phone' => '01022222222',
            'status' => 'pending',
        ]);
    }

    public function test_customer_can_only_access_own_bookings_and_cancel_pending_booking(): void
    {
        $customer = User::factory()->create();
        $otherCustomer = User::factory()->create();
        $booking = Booking::factory()->create(['user_id' => $customer->id, 'vehicle_id' => Vehicle::factory()->create(['user_id' => $customer->id])->id]);
        $otherBooking = Booking::factory()->create(['user_id' => $otherCustomer->id, 'vehicle_id' => Vehicle::factory()->create(['user_id' => $otherCustomer->id])->id]);

        $this->actingAs($customer, 'sanctum')->getJson('/api/bookings')
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.id', $booking->id);

        $this->actingAs($customer, 'sanctum')->getJson("/api/bookings/{$otherBooking->id}")
            ->assertNotFound();

        $this->actingAs($customer, 'sanctum')->putJson("/api/bookings/{$booking->id}/cancel", [
            'notes' => 'Changed my mind',
        ])
            ->assertOk()
            ->assertJsonPath('data.booking.status', BookingStatus::Cancelled->value);
    }

    public function test_workshop_owner_can_manage_own_booking_status_flow_and_creates_ledger_on_complete(): void
    {
        $owner = User::factory()->create(['role' => UserRole::WorkshopOwner]);
        $customer = User::factory()->create();
        $workshop = Workshop::factory()->approved()->create(['owner_id' => $owner->id]);
        $service = Service::factory()->create(['name' => 'Oil Change', 'slug' => 'oil-change']);
        $vehicle = Vehicle::factory()->create(['user_id' => $customer->id, 'mileage_km' => 12345]);
        $booking = Booking::factory()->create([
            'user_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'service_id' => $service->id,
            'status' => BookingStatus::Pending,
        ]);

        $this->actingAs($owner, 'sanctum')->getJson('/api/workshop/bookings')
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.id', $booking->id);

        $this->actingAs($owner, 'sanctum')->putJson("/api/workshop/bookings/{$booking->id}/accept")
            ->assertOk()
            ->assertJsonPath('data.booking.status', BookingStatus::Accepted->value);

        $this->actingAs($owner, 'sanctum')->putJson("/api/workshop/bookings/{$booking->id}/start")
            ->assertOk()
            ->assertJsonPath('data.booking.status', BookingStatus::InProgress->value);

        $this->actingAs($owner, 'sanctum')->putJson("/api/workshop/bookings/{$booking->id}/complete")
            ->assertOk()
            ->assertJsonPath('data.booking.status', BookingStatus::Completed->value);

        $this->assertDatabaseHas('service_ledgers', [
            'booking_id' => $booking->id,
            'vehicle_id' => $vehicle->id,
            'title' => 'Oil Change',
            'mileage_km' => 12345,
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $customer->id,
            'type' => 'booking_status_updated',
        ]);
    }

    public function test_invalid_workshop_booking_transition_is_rejected(): void
    {
        $owner = User::factory()->create(['role' => UserRole::WorkshopOwner]);
        $workshop = Workshop::factory()->approved()->create(['owner_id' => $owner->id]);
        $booking = Booking::factory()->create(['workshop_id' => $workshop->id, 'status' => BookingStatus::Pending]);

        $this->actingAs($owner, 'sanctum')->putJson("/api/workshop/bookings/{$booking->id}/complete")
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }
}
