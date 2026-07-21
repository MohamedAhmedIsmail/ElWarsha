<?php

namespace App\Repositories\Contracts;

use App\Enums\WorkshopAnalyticsEventType;
use App\Models\WorkshopAnalyticsEvent;

interface WorkshopAnalyticsRepositoryInterface
{
    /**
     * @param array<string, mixed>|null $metadata
     */
    public function create(int $workshopId, ?int $userId, WorkshopAnalyticsEventType $eventType, ?array $metadata = null): WorkshopAnalyticsEvent;
}
