<?php

namespace Database\Factories;

use App\Models\MaintenanceItem;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceLedger>
 */
class ServiceLedgerFactory extends Factory
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
            'workshop_id' => Workshop::factory()->approved(),
            'booking_id' => null,
            'diagnosis_id' => null,
            'sos_request_id' => null,
            'maintenance_item_id' => MaintenanceItem::factory(),
            'title' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'service_date' => now()->subDays(fake()->numberBetween(1, 120))->toDateString(),
            'cost' => fake()->randomFloat(2, 100, 5000),
            'mileage_km' => fake()->numberBetween(10000, 200000),
            'invoice_file' => null,
        ];
    }
}
