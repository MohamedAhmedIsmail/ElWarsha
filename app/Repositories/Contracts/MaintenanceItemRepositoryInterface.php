<?php

namespace App\Repositories\Contracts;

use App\Models\MaintenanceItem;
use Illuminate\Database\Eloquent\Collection;

interface MaintenanceItemRepositoryInterface
{
    /**
     * @return Collection<int, MaintenanceItem>
     */
    public function listActive(): Collection;

    public function findActive(int $maintenanceItemId): ?MaintenanceItem;
}
