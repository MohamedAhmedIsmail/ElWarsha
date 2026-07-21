<?php

namespace Tests\Feature;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Enums\UserRole;
use App\Enums\WorkshopAnalyticsEventType;
use App\Models\Booking;
use App\Models\Lead;
use App\Models\SosRequest;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use App\Models\WorkshopAnalyticsEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopCrmApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_tracking_call_creates_analytics_event_and_lead_with_metadata(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $workshop = Workshop::factory()->approved()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/workshops/{$workshop->id}/track-call", [
            'vehicle_id' => $vehicle->id,
            'latitude' => 30.0444,
            'longitude' => 31.2357,
            'metadata' => ['screen' => 'profile'],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.event.event_type', WorkshopAnalyticsEventType::CallClick->value)
            ->assertJsonPath('data.event.metadata.screen', 'profile');

        $this->assertDatabaseHas('workshop_analytics_events', [
            'workshop_id' => $workshop->id,
            'user_id' => $user->id,
            'event_type' => WorkshopAnalyticsEventType::CallClick->value,
        ]);
        $this->assertDatabaseHas('leads', [
            'workshop_id' => $workshop->id,
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'source' => LeadSource::CallClick->value,
            'status' => LeadStatus::New->value,
        ]);
    }

    public function test_profile_view_lead_is_not_duplicated_for_same_user_same_day(): void
    {
        $user = User::factory()->create();
        $workshop = Workshop::factory()->approved()->create();

        $this->actingAs($user, 'sanctum')->postJson("/api/workshops/{$workshop->id}/track-view")
            ->assertCreated();
        $this->actingAs($user, 'sanctum')->postJson("/api/workshops/{$workshop->id}/track-view")
            ->assertCreated();

        $this->assertSame(2, WorkshopAnalyticsEvent::query()
            ->where('workshop_id', $workshop->id)
            ->where('event_type', WorkshopAnalyticsEventType::ProfileView)
            ->count());
        $this->assertSame(1, Lead::query()
            ->where('workshop_id', $workshop->id)
            ->where('user_id', $user->id)
            ->where('source', LeadSource::ProfileView)
            ->count());
    }

    public function test_tracking_rejects_vehicle_not_owned_by_authenticated_user(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create();
        $workshop = Workshop::factory()->approved()->create();

        $this->actingAs($user, 'sanctum')->postJson("/api/workshops/{$workshop->id}/track-whatsapp", [
            'vehicle_id' => $vehicle->id,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['vehicle_id']);
    }

    public function test_workshop_owner_can_list_filter_and_show_own_leads(): void
    {
        $owner = User::factory()->create(['role' => UserRole::WorkshopOwner]);
        $workshop = Workshop::factory()->approved()->create(['owner_id' => $owner->id]);
        $callLead = Lead::factory()->create([
            'workshop_id' => $workshop->id,
            'source' => LeadSource::CallClick,
            'status' => LeadStatus::New,
        ]);
        Lead::factory()->create([
            'workshop_id' => $workshop->id,
            'source' => LeadSource::WhatsappClick,
            'status' => LeadStatus::Contacted,
        ]);
        Lead::factory()->create();

        $this->actingAs($owner, 'sanctum')->getJson('/api/workshop/leads?source=call_click&status=new')
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.id', $callLead->id);

        $this->actingAs($owner, 'sanctum')->getJson("/api/workshop/leads/{$callLead->id}")
            ->assertOk()
            ->assertJsonPath('data.lead.id', $callLead->id);
    }

    public function test_workshop_owner_cannot_access_other_workshop_lead(): void
    {
        $owner = User::factory()->create(['role' => UserRole::WorkshopOwner]);
        Workshop::factory()->approved()->create(['owner_id' => $owner->id]);
        $otherLead = Lead::factory()->create();

        $this->actingAs($owner, 'sanctum')->getJson("/api/workshop/leads/{$otherLead->id}")
            ->assertNotFound();
    }

    public function test_workshop_owner_can_update_lead_status_and_add_note(): void
    {
        $owner = User::factory()->create(['role' => UserRole::WorkshopOwner]);
        $workshop = Workshop::factory()->approved()->create(['owner_id' => $owner->id]);
        $lead = Lead::factory()->create([
            'workshop_id' => $workshop->id,
            'status' => LeadStatus::New,
        ]);

        $this->actingAs($owner, 'sanctum')->putJson("/api/workshop/leads/{$lead->id}/status", [
            'status' => LeadStatus::Contacted->value,
        ])
            ->assertOk()
            ->assertJsonPath('data.lead.status', LeadStatus::Contacted->value);

        $this->assertDatabaseHas('lead_status_logs', [
            'lead_id' => $lead->id,
            'old_status' => LeadStatus::New->value,
            'new_status' => LeadStatus::Contacted->value,
            'changed_by' => $owner->id,
        ]);

        $this->actingAs($owner, 'sanctum')->postJson("/api/workshop/leads/{$lead->id}/notes", [
            'note' => 'Customer asked for a follow-up tomorrow.',
        ])
            ->assertCreated()
            ->assertJsonPath('data.note.note', 'Customer asked for a follow-up tomorrow.');
    }

    public function test_crm_analytics_returns_lead_and_click_counts(): void
    {
        $owner = User::factory()->create(['role' => UserRole::WorkshopOwner]);
        $workshop = Workshop::factory()->approved()->create(['owner_id' => $owner->id]);

        Lead::factory()->create(['workshop_id' => $workshop->id, 'source' => LeadSource::CallClick, 'status' => LeadStatus::New]);
        Lead::factory()->create(['workshop_id' => $workshop->id, 'source' => LeadSource::Booking, 'status' => LeadStatus::Booked]);
        Lead::factory()->create(['workshop_id' => $workshop->id, 'source' => LeadSource::Sos, 'status' => LeadStatus::New]);
        Lead::factory()->create();

        WorkshopAnalyticsEvent::factory()->create(['workshop_id' => $workshop->id, 'event_type' => WorkshopAnalyticsEventType::CallClick]);
        WorkshopAnalyticsEvent::factory()->create(['workshop_id' => $workshop->id, 'event_type' => WorkshopAnalyticsEventType::WhatsappClick]);
        WorkshopAnalyticsEvent::factory()->create(['workshop_id' => $workshop->id, 'event_type' => WorkshopAnalyticsEventType::DirectionsClick]);
        WorkshopAnalyticsEvent::factory()->create(['event_type' => WorkshopAnalyticsEventType::CallClick]);

        $this->actingAs($owner, 'sanctum')->getJson('/api/workshop/crm/analytics')
            ->assertOk()
            ->assertJsonPath('data.analytics.total_leads', 3)
            ->assertJsonPath('data.analytics.leads_by_source.call_click', 1)
            ->assertJsonPath('data.analytics.leads_by_status.new', 2)
            ->assertJsonPath('data.analytics.call_clicks_count', 1)
            ->assertJsonPath('data.analytics.whatsapp_clicks_count', 1)
            ->assertJsonPath('data.analytics.directions_clicks_count', 1)
            ->assertJsonPath('data.analytics.bookings_count', 1)
            ->assertJsonPath('data.analytics.sos_leads_count', 1);
    }

    public function test_booking_and_sos_leads_count_in_crm_analytics(): void
    {
        $owner = User::factory()->create(['role' => UserRole::WorkshopOwner]);
        $workshop = Workshop::factory()->approved()->create(['owner_id' => $owner->id]);
        $booking = Booking::factory()->create(['workshop_id' => $workshop->id]);
        $sosRequest = SosRequest::factory()->create();

        Lead::query()->create([
            'workshop_id' => $workshop->id,
            'user_id' => $booking->user_id,
            'vehicle_id' => $booking->vehicle_id,
            'booking_id' => $booking->id,
            'source' => LeadSource::Booking,
            'status' => LeadStatus::New,
        ]);
        Lead::query()->create([
            'workshop_id' => $workshop->id,
            'user_id' => $sosRequest->user_id,
            'vehicle_id' => $sosRequest->vehicle_id,
            'sos_request_id' => $sosRequest->id,
            'source' => LeadSource::Sos,
            'status' => LeadStatus::New,
        ]);

        $this->actingAs($owner, 'sanctum')->getJson('/api/workshop/crm/analytics')
            ->assertOk()
            ->assertJsonPath('data.analytics.bookings_count', 1)
            ->assertJsonPath('data.analytics.sos_leads_count', 1);
    }
}
