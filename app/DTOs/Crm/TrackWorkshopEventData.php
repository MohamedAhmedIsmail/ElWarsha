<?php

namespace App\DTOs\Crm;

class TrackWorkshopEventData
{
    /**
     * @param array<string, mixed>|null $metadata
     */
    public function __construct(
        public readonly ?int $vehicleId,
        public readonly ?float $latitude,
        public readonly ?float $longitude,
        public readonly ?array $metadata,
    ) {
    }
}
