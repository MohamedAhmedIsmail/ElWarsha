<?php

namespace App\Repositories\Eloquent;

use App\Models\MaintenanceItem;
use App\Repositories\Contracts\MaintenanceItemRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MaintenanceItemRepository implements MaintenanceItemRepositoryInterface
{
    public function listActive(): Collection
    {
        return MaintenanceItem::query()
            ->active()
            ->with('serviceCategory')
            ->orderBy('name')
            ->get();
    }

    public function findActive(int $maintenanceItemId): ?MaintenanceItem
    {
        return MaintenanceItem::query()
            ->active()
            ->whereKey($maintenanceItemId)
            ->first();
    }
}
