<?php

namespace App\Repositories\Eloquent;

use App\Enums\WorkshopAnalyticsEventType;
use App\Models\WorkshopAnalyticsEvent;
use App\Repositories\Contracts\WorkshopAnalyticsRepositoryInterface;

class WorkshopAnalyticsRepository implements WorkshopAnalyticsRepositoryInterface
{
    public function create(int $workshopId, ?int $userId, WorkshopAnalyticsEventType $eventType, ?array $metadata = null): WorkshopAnalyticsEvent
    {
        return WorkshopAnalyticsEvent::query()->create([
            'workshop_id' => $workshopId,
            'user_id' => $userId,
            'event_type' => $eventType,
            'metadata' => $metadata,
        ]);
    }
}
