<?php

namespace Database\Seeders;

use App\Enums\RecordStatus;
use App\Models\SosServiceType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SosServiceTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Towing',
            'Dead Battery',
            'Flat Tire',
            'Overheating',
            'Car Not Starting',
            'Out of Fuel',
            'Accident Support',
            'Locked Key',
            'Brake Emergency',
            'Electrical Emergency',
        ];

        foreach ($types as $type) {
            SosServiceType::query()->updateOrCreate(
                ['slug' => Str::slug($type)],
                ['name' => $type, 'status' => RecordStatus::Active]
            );
        }
    }
}
