<?php

namespace App\Repositories\Eloquent;

use App\Models\Workshop;
use App\Repositories\Contracts\WorkshopWorkingHourRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class WorkshopWorkingHourRepository implements WorkshopWorkingHourRepositoryInterface
{
    public function sync(Workshop $workshop, array $hours): Workshop
    {
        foreach ($hours as $hour) {
            $workshop->workingHours()->updateOrCreate(
                ['day_of_week' => $hour['day_of_week']],
                [
                    'opens_at' => (bool) $hour['is_closed'] ? null : $hour['opens_at'],
                    'closes_at' => (bool) $hour['is_closed'] ? null : $hour['closes_at'],
                    'is_closed' => (bool) $hour['is_closed'],
                ]
            );
        }

        return $workshop->refresh()->load(['workingHours']);
    }

    public function listForWorkshop(Workshop $workshop): Collection
    {
        return $workshop->workingHours()
            ->orderByRaw("FIELD(day_of_week, 'saturday','sunday','monday','tuesday','wednesday','thursday','friday')")
            ->get();
    }
}
