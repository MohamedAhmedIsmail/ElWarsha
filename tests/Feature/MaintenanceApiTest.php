<?php

namespace Tests\Feature;

use App\Enums\MaintenanceReminderStatus;
use App\Enums\RecordStatus;
use App\Models\MaintenanceItem;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleMaintenanceReminder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_maintenance_items_return_active_only(): void
    {
        MaintenanceItem::factory()->create(['name' => 'Oil Change']);
        MaintenanceItem::factory()->inactive()->create(['name' => 'Hidden Item']);

        $this->getJson('/api/maintenance-items')
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.name', 'Oil Change');
    }

    public function test_user_can_create_and_list_vehicle_maintenance_reminders(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $item = MaintenanceItem::factory()->create(['name' => 'Oil Change']);

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/vehicles/{$vehicle->id}/maintenance-reminders", [
            'maintenance_item_id' => $item->id,
            'last_done_at' => now()->subMonth()->toDateString(),
            'last_done_mileage' => 40000,
            'next_due_at' => now()->addMonth()->toDateString(),
            'next_due_mileage' => 45000,
            'reminder_before_days' => 10,
            'notes' => 'Use synthetic oil.',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.maintenance_reminder.vehicle_id', $vehicle->id)
            ->assertJsonPath('data.maintenance_reminder.maintenance_item_id', $item->id)
            ->assertJsonPath('data.maintenance_reminder.status', MaintenanceReminderStatus::Active->value);

        $this->actingAs($user, 'sanctum')->getJson("/api/vehicles/{$vehicle->id}/maintenance-reminders")
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.maintenance_item.name', 'Oil Change');
    }

    public function test_user_cannot_access_reminders_for_another_users_vehicle(): void
    {
        $user = User::factory()->create();
        $otherVehicle = Vehicle::factory()->create();
        $item = MaintenanceItem::factory()->create();

        $this->actingAs($user, 'sanctum')->getJson("/api/vehicles/{$otherVehicle->id}/maintenance-reminders")
            ->assertNotFound();

        $this->actingAs($user, 'sanctum')->postJson("/api/vehicles/{$otherVehicle->id}/maintenance-reminders", [
            'maintenance_item_id' => $item->id,
            'next_due_at' => now()->addWeek()->toDateString(),
        ])
            ->assertNotFound();
    }

    public function test_user_can_update_and_delete_own_maintenance_reminder(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $reminder = VehicleMaintenanceReminder::factory()->create([
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'status' => MaintenanceReminderStatus::Active,
        ]);

        $this->actingAs($user, 'sanctum')->putJson("/api/maintenance-reminders/{$reminder->id}", [
            'last_done_at' => now()->toDateString(),
            'last_done_mileage' => 50000,
            'next_due_at' => now()->addMonths(6)->toDateString(),
            'next_due_mileage' => 60000,
            'status' => MaintenanceReminderStatus::Done->value,
            'notes' => 'Done today.',
        ])
            ->assertOk()
            ->assertJsonPath('data.maintenance_reminder.status', MaintenanceReminderStatus::Done->value)
            ->assertJsonPath('data.maintenance_reminder.last_done_mileage', 50000);

        $this->actingAs($user, 'sanctum')->deleteJson("/api/maintenance-reminders/{$reminder->id}")
            ->assertOk();

        $this->assertDatabaseMissing('vehicle_maintenance_reminders', ['id' => $reminder->id]);
    }

    public function test_user_cannot_update_another_users_maintenance_reminder(): void
    {
        $user = User::factory()->create();
        $reminder = VehicleMaintenanceReminder::factory()->create();

        $this->actingAs($user, 'sanctum')->putJson("/api/maintenance-reminders/{$reminder->id}", [
            'status' => MaintenanceReminderStatus::Skipped->value,
        ])
            ->assertNotFound();
    }

    public function test_upcoming_reminders_include_due_within_fourteen_days_or_due_mileage(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id, 'mileage_km' => 50000]);

        $dueByDate = VehicleMaintenanceReminder::factory()->create([
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'next_due_at' => now()->addDays(10)->toDateString(),
            'next_due_mileage' => 80000,
            'status' => MaintenanceReminderStatus::Active,
        ]);
        $dueByMileage = VehicleMaintenanceReminder::factory()->create([
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'next_due_at' => now()->addMonths(3)->toDateString(),
            'next_due_mileage' => 49000,
            'status' => MaintenanceReminderStatus::Active,
        ]);
        VehicleMaintenanceReminder::factory()->create([
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'next_due_at' => now()->addMonths(3)->toDateString(),
            'next_due_mileage' => 70000,
            'status' => MaintenanceReminderStatus::Active,
        ]);
        VehicleMaintenanceReminder::factory()->create([
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'next_due_at' => now()->addDays(3)->toDateString(),
            'status' => MaintenanceReminderStatus::Cancelled,
        ]);
        VehicleMaintenanceReminder::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/maintenance-reminders/upcoming')
            ->assertOk()
            ->assertJsonCount(2, 'data.items');

        $ids = collect($response->json('data.items'))->pluck('id')->all();
        $this->assertContains($dueByDate->id, $ids);
        $this->assertContains($dueByMileage->id, $ids);
    }

    public function test_inactive_maintenance_item_cannot_be_used_for_reminder(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        $item = MaintenanceItem::factory()->inactive()->create();

        $this->actingAs($user, 'sanctum')->postJson("/api/vehicles/{$vehicle->id}/maintenance-reminders", [
            'maintenance_item_id' => $item->id,
            'next_due_at' => now()->addWeek()->toDateString(),
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['maintenance_item_id']);
    }
}
