<?php

namespace Database\Factories;

use App\Models\Diagnosis;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiagnosisWorkshopSuggestion>
 */
class DiagnosisWorkshopSuggestionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'diagnosis_id' => Diagnosis::factory(),
            'workshop_id' => Workshop::factory()->approved(),
            'score' => fake()->randomFloat(2, 1, 100),
            'reason' => fake()->sentence(),
        ];
    }
}
