<?php

namespace App\DTOs\Workshop;

final readonly class WorkshopFilterData
{
    public function __construct(
        public ?int $serviceId,
        public ?int $categoryId,
        public ?int $brandId,
        public ?string $city,
        public ?string $area,
        public ?float $lat,
        public ?float $lng,
        public float $radius,
        public ?float $rating,
        public ?bool $isVerified,
        public ?bool $openNow,
        public ?bool $acceptsBooking,
        public ?bool $acceptsSos,
        public ?string $search,
        public int $perPage,
    ) {
    }
}
