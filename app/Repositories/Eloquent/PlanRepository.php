<?php

namespace App\Repositories\Eloquent;

use App\Models\Plan;
use App\Repositories\Contracts\PlanRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PlanRepository implements PlanRepositoryInterface
{
    public function listActive(): Collection
    {
        return Plan::query()
            ->active()
            ->orderBy('price')
            ->orderBy('id')
            ->get();
    }

    public function findActive(int $planId): ?Plan
    {
        return Plan::query()
            ->active()
            ->whereKey($planId)
            ->first();
    }
}
