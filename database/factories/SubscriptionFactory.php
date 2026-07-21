<?php

namespace Database\Factories;

use App\Enums\SubscriptionStatus;
use App\Models\Plan;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workshop_id' => Workshop::factory()->approved(),
            'plan_id' => Plan::factory(),
            'starts_at' => now()->toDateString(),
            'ends_at' => now()->addDays(30)->toDateString(),
            'status' => SubscriptionStatus::Pending,
            'auto_renew' => false,
        ];
    }
}
