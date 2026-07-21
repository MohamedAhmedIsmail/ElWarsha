<?php

namespace App\DTOs\EmergencyGuidance;

class StoreEmergencyGuidanceData
{
    /**
     * @param array<int, string>|null $symptoms
     */
    public function __construct(
        public readonly ?int $vehicleId,
        public readonly string $description,
        public readonly ?array $symptoms,
        public readonly ?float $latitude,
        public readonly ?float $longitude,
    ) {
    }
}
