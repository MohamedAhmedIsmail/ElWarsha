<?php

namespace App\Repositories\Contracts;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Collection;

interface PlanRepositoryInterface
{
    /**
     * @return Collection<int, Plan>
     */
    public function listActive(): Collection;

    public function findActive(int $planId): ?Plan;
}
