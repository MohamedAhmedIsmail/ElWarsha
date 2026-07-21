<?php

namespace Database\Factories;

use App\Enums\DiagnosisConfidence;
use App\Enums\DiagnosisStatus;
use App\Enums\DiagnosisUrgency;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Diagnosis>
 */
class DiagnosisFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();

        return [
            'user_id' => $user->id,
            'vehicle_id' => Vehicle::factory()->create(['user_id' => $user->id])->id,
            'description' => fake()->sentence(),
            'symptoms_json' => null,
            'ai_response' => null,
            'diagnosis_text' => null,
            'confidence' => null,
            'urgency' => null,
            'affected_category_id' => null,
            'recommend_professional' => true,
            'status' => DiagnosisStatus::Pending,
            'disclaimer_accepted' => false,
        ];
    }

    public function completed(?ServiceCategory $category = null): static
    {
        return $this->state(fn (array $attributes): array => [
            'ai_response' => [
                'diagnosis' => 'Possible mechanical issue',
                'confidence' => DiagnosisConfidence::Medium->value,
                'urgency' => DiagnosisUrgency::Medium->value,
            ],
            'diagnosis_text' => 'Possible mechanical issue',
            'confidence' => DiagnosisConfidence::Medium,
            'urgency' => DiagnosisUrgency::Medium,
            'affected_category_id' => $category?->id,
            'status' => DiagnosisStatus::Completed,
        ]);
    }
}
