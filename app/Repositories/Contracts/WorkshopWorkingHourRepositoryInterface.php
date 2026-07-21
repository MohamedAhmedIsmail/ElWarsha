<?php

namespace App\Repositories\Contracts;

use App\Models\Workshop;
use Illuminate\Database\Eloquent\Collection;

interface WorkshopWorkingHourRepositoryInterface
{
    /**
     * @param array<int, array{day_of_week: string, opens_at: string|null, closes_at: string|null, is_closed: bool}> $hours
     */
    public function sync(Workshop $workshop, array $hours): Workshop;

    /**
     * @return Collection<int, \App\Models\WorkshopWorkingHour>
     */
    public function listForWorkshop(Workshop $workshop): Collection;
}
