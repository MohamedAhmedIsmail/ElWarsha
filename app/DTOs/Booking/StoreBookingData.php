<?php

namespace App\DTOs\Booking;

final readonly class StoreBookingData
{
    public function __construct(
        public int $vehicleId,
        public int $workshopId,
        public ?int $diagnosisId,
        public ?int $serviceId,
        public ?string $scheduledAt,
        public ?string $description,
    ) {
    }
}
