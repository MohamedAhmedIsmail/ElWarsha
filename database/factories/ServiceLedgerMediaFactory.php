<?php

namespace Database\Factories;

use App\Enums\ServiceLedgerMediaType;
use App\Models\ServiceLedger;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceLedgerMedia>
 */
class ServiceLedgerMediaFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_ledger_id' => ServiceLedger::factory(),
            'media_type' => ServiceLedgerMediaType::Image,
            'file_path' => fake()->imageUrl(),
        ];
    }
}
