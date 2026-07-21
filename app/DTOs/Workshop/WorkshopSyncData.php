<?php

namespace App\DTOs\Workshop;

final readonly class WorkshopSyncData
{
    /**
     * @param array<int, int> $ids
     */
    public function __construct(public array $ids)
    {
    }
}
