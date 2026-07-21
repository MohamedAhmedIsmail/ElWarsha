<?php

namespace Database\Seeders;

use App\Enums\RecordStatus;
use App\Models\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            ['name' => 'Free', 'price' => 0, 'duration_days' => 30, 'is_featured' => false],
            ['name' => 'Basic', 'price' => 299, 'duration_days' => 30, 'is_featured' => false],
            ['name' => 'Pro', 'price' => 699, 'duration_days' => 30, 'is_featured' => true],
            ['name' => 'Premium', 'price' => 1299, 'duration_days' => 30, 'is_featured' => false],
        ];

        foreach ($plans as $plan) {
            Plan::query()->updateOrCreate(
                ['code' => Str::slug($plan['name'])],
                [
                    ...$plan,
                    'description' => $plan['name'] . ' workshop subscription plan.',
                    'features' => [],
                    'status' => RecordStatus::Active,
                ]
            );
        }
    }
}
