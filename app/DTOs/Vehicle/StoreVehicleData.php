<?php

namespace App\DTOs\Vehicle;

final readonly class StoreVehicleData
{
    public function __construct(
        public int $carBrandId,
        public int $carModelId,
        public ?int $year,
        public ?int $mileageKm,
        public ?string $plateNumber,
        public ?string $vin,
        public ?string $color,
        public ?string $image,
        public ?string $notes,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'car_brand_id' => $this->carBrandId,
            'car_model_id' => $this->carModelId,
            'year' => $this->year,
            'mileage_km' => $this->mileageKm,
            'plate_number' => $this->plateNumber,
            'vin' => $this->vin,
            'color' => $this->color,
            'image' => $this->image,
            'notes' => $this->notes,
        ];
    }
}
