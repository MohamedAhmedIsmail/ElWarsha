<?php

namespace App\DTOs\Workshop;

final readonly class WorkshopData
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
