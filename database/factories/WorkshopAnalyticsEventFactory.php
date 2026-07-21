<?php

namespace Database\Factories;

use App\Enums\WorkshopAnalyticsEventType;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkshopAnalyticsEvent>
 */
class WorkshopAnalyticsEventFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workshop_id' => Workshop::factory()->approved(),
            'user_id' => User::factory(),
            'event_type' => WorkshopAnalyticsEventType::ProfileView,
            'latitude' => 30.0444,
            'longitude' => 31.2357,
            'metadata' => ['source' => 'test'],
        ];
    }
}
