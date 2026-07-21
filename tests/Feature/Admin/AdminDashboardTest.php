<?php

namespace Tests\Feature\Admin;

use App\Enums\BookingStatus;
use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Enums\PaymentStatus;
use App\Enums\SosRequestStatus;
use App\Enums\SubscriptionStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Diagnosis;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Review;
use App\Models\ServiceLedger;
use App\Models\SosRequest;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_kpis_and_latest_tables(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $customer = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $customer->id]);
        $pendingWorkshop = Workshop::factory()->create(['name' => 'Pending Shop']);
        $approvedWorkshop = Workshop::factory()->approved()->create(['name' => 'Approved Shop']);
        $booking = Booking::factory()->create([
            'user_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $approvedWorkshop->id,
            'status' => BookingStatus::Pending,
        ]);
        $sosRequest = SosRequest::factory()->create([
            'user_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'status' => SosRequestStatus::Pending,
        ]);
        Diagnosis::factory()->create(['user_id' => $customer->id, 'vehicle_id' => $vehicle->id]);
        Review::factory()->create(['workshop_id' => $approvedWorkshop->id]);
        ServiceLedger::factory()->create(['user_id' => $customer->id, 'vehicle_id' => $vehicle->id]);
        $subscription = Subscription::factory()->create([
            'workshop_id' => $approvedWorkshop->id,
            'status' => SubscriptionStatus::Active,
        ]);
        Payment::factory()->create([
            'workshop_id' => $approvedWorkshop->id,
            'subscription_id' => $subscription->id,
            'status' => PaymentStatus::Approved,
            'approved_at' => now(),
            'amount' => 500,
        ]);
        Lead::factory()->count(2)->create([
            'workshop_id' => $approvedWorkshop->id,
            'source' => LeadSource::Booking,
            'status' => LeadStatus::New,
        ]);

        $this->actingAs($admin)->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Total Users')
            ->assertSee('Total Vehicles')
            ->assertSee('Pending Workshops')
            ->assertSee('Active Subscriptions')
            ->assertSee('Monthly Revenue')
            ->assertSee('Latest Pending Workshops')
            ->assertSee('Latest Bookings')
            ->assertSee('Latest SOS Requests')
            ->assertSee('Latest Payments')
            ->assertSee('Top Workshops by Leads')
            ->assertSee('Pending Shop')
            ->assertSee('Approved Shop')
            ->assertSee('#' . $booking->id)
            ->assertSee('#' . $sosRequest->id)
            ->assertSee('500.00 EGP');
    }
}
