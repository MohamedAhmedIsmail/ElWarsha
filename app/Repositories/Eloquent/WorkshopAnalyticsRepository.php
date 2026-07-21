<?php

namespace App\Repositories\Eloquent;

use App\Enums\WorkshopAnalyticsEventType;
use App\Models\Workshop;
use App\Models\WorkshopAnalyticsEvent;
use App\Repositories\Contracts\WorkshopAnalyticsRepositoryInterface;

class WorkshopAnalyticsRepository implements WorkshopAnalyticsRepositoryInterface
{
    public function create(
        int $workshopId,
        ?int $userId,
        WorkshopAnalyticsEventType $eventType,
        ?float $latitude = null,
        ?float $longitude = null,
        ?array $metadata = null
    ): WorkshopAnalyticsEvent
    {
        return WorkshopAnalyticsEvent::query()->create([
            'workshop_id' => $workshopId,
            'user_id' => $userId,
            'event_type' => $eventType,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'metadata' => $metadata,
        ]);
    }

    public function countsByEventType(Workshop $workshop): array
    {
        return WorkshopAnalyticsEvent::query()
            ->where('workshop_id', $workshop->id)
            ->selectRaw('event_type, COUNT(*) as aggregate')
            ->groupBy('event_type')
            ->pluck('aggregate', 'event_type')
            ->map(fn ($count) => (int) $count)
            ->all();
    }
}
