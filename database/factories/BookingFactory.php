<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Models\Service;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();
        $workshop = Workshop::factory()->approved()->create(['accepts_booking' => true]);

        return [
            'user_id' => $user->id,
            'vehicle_id' => Vehicle::factory()->create(['user_id' => $user->id])->id,
            'workshop_id' => $workshop->id,
            'diagnosis_id' => null,
            'service_id' => Service::factory()->create()->id,
            'scheduled_at' => now()->addDay(),
            'description' => fake()->sentence(),
            'status' => BookingStatus::Pending,
        ];
    }
}
