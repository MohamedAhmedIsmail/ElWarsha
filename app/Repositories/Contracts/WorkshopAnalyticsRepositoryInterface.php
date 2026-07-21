<?php

namespace App\Repositories\Contracts;

use App\Enums\WorkshopAnalyticsEventType;
use App\Models\Workshop;
use App\Models\WorkshopAnalyticsEvent;

interface WorkshopAnalyticsRepositoryInterface
{
    /**
     * @param array<string, mixed>|null $metadata
     */
    public function create(
        int $workshopId,
        ?int $userId,
        WorkshopAnalyticsEventType $eventType,
        ?float $latitude = null,
        ?float $longitude = null,
        ?array $metadata = null
    ): WorkshopAnalyticsEvent;

    /**
     * @return array<string, int>
     */
    public function countsByEventType(Workshop $workshop): array;
}
