<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookingStatusLog>
 */
class BookingStatusLogFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'old_status' => null,
            'new_status' => BookingStatus::Pending->value,
            'changed_by' => null,
            'notes' => null,
        ];
    }
}
