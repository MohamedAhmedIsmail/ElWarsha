<?php

namespace Database\Seeders;

use App\Enums\RecordStatus;
use App\Models\MaintenanceItem;
use Illuminate\Database\Seeder;

class MaintenanceItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            'Oil Change',
            'Oil Filter',
            'Air Filter',
            'Fuel Filter',
            'AC Filter',
            'Battery',
            'Tires',
            'Brakes',
            'AC Check',
            'License Renewal',
            'Insurance Renewal',
            'Pre-travel Check',
        ];

        foreach ($items as $item) {
            MaintenanceItem::query()->updateOrCreate(
                ['name' => $item],
                ['status' => RecordStatus::Active]
            );
        }
    }
}
