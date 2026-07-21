<?php

namespace Database\Factories;

use App\Enums\MaintenanceReminderStatus;
use App\Models\MaintenanceItem;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VehicleMaintenanceReminder>
 */
class VehicleMaintenanceReminderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();

        return [
            'user_id' => $user->id,
            'vehicle_id' => Vehicle::factory()->create(['user_id' => $user->id])->id,
            'maintenance_item_id' => MaintenanceItem::factory(),
            'last_done_at' => fake()->optional()->dateTimeBetween('-1 year', '-1 month')?->format('Y-m-d'),
            'last_done_mileage' => fake()->optional()->numberBetween(0, 90000),
            'next_due_at' => fake()->optional()->dateTimeBetween('+1 week', '+6 months')?->format('Y-m-d'),
            'next_due_mileage' => fake()->optional()->numberBetween(10000, 120000),
            'reminder_before_days' => 7,
            'status' => MaintenanceReminderStatus::Active,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
