<?php

namespace App\Repositories\Contracts;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Workshop;

interface SubscriptionRepositoryInterface
{
    public function latestForWorkshop(Workshop $workshop): ?Subscription;

    public function createPending(Workshop $workshop, Plan $plan): Subscription;
}
