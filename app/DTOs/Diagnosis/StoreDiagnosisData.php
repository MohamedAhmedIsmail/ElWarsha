<?php

namespace App\DTOs\Diagnosis;

final readonly class StoreDiagnosisData
{
    /**
     * @param array<string, mixed>|null $symptomsJson
     */
    public function __construct(
        public int $vehicleId,
        public string $description,
        public ?array $symptomsJson,
        public bool $disclaimerAccepted,
    ) {
    }
}
