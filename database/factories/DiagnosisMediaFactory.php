<?php

namespace Database\Factories;

use App\Enums\DiagnosisMediaType;
use App\Models\Diagnosis;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiagnosisMedia>
 */
class DiagnosisMediaFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'diagnosis_id' => Diagnosis::factory(),
            'media_type' => DiagnosisMediaType::Image,
            'file_path' => 'diagnoses/example.jpg',
        ];
    }
}
