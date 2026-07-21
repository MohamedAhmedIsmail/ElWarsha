<?php

namespace App\DTOs\Workshop;

final readonly class WorkingHoursData
{
    /**
     * @param array<int, array{day_of_week: string, opens_at: string|null, closes_at: string|null, is_closed: bool}> $hours
     */
    public function __construct(public array $hours)
    {
    }
}
