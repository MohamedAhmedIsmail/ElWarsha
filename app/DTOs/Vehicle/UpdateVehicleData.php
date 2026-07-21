<?php

namespace App\DTOs\Vehicle;

final readonly class UpdateVehicleData
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(public array $attributes)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
