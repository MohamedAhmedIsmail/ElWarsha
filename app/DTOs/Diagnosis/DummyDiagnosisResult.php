<?php

namespace App\DTOs\Diagnosis;

use App\Enums\DiagnosisConfidence;
use App\Enums\DiagnosisUrgency;
use App\Models\ServiceCategory;

final readonly class DummyDiagnosisResult
{
    /**
     * @param array<string, mixed> $aiResponse
     */
    public function __construct(
        public array $aiResponse,
        public string $diagnosisText,
        public DiagnosisConfidence $confidence,
        public DiagnosisUrgency $urgency,
        public ?ServiceCategory $affectedCategory,
        public bool $recommendProfessional,
    ) {
    }
}
