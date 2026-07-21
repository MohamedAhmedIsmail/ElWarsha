<?php

namespace Database\Factories;

use App\Enums\DayOfWeek;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkshopWorkingHour>
 */
class WorkshopWorkingHourFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workshop_id' => Workshop::factory(),
            'day_of_week' => DayOfWeek::Monday,
            'opens_at' => '09:00',
            'closes_at' => '18:00',
            'is_closed' => false,
        ];
    }
}
