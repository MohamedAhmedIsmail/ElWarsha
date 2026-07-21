<?php

namespace App\Services;

use App\Models\Plan;
use App\Repositories\Contracts\PlanRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PlanService
{
    public function __construct(private readonly PlanRepositoryInterface $plans)
    {
    }

    /**
     * @return Collection<int, Plan>
     */
    public function listActive(): Collection
    {
        return $this->plans->listActive();
    }
}
