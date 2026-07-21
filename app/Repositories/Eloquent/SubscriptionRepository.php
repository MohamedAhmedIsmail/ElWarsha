<?php

namespace App\Repositories\Eloquent;

use App\Enums\SubscriptionStatus;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Workshop;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;

class SubscriptionRepository implements SubscriptionRepositoryInterface
{
    public function latestForWorkshop(Workshop $workshop): ?Subscription
    {
        return $workshop->subscriptions()
            ->with(['plan', 'payments'])
            ->latest('id')
            ->first();
    }

    public function createPending(Workshop $workshop, Plan $plan): Subscription
    {
        $startsAt = now()->toDateString();

        return $workshop->subscriptions()->create([
            'plan_id' => $plan->id,
            'starts_at' => $startsAt,
            'ends_at' => now()->addDays($plan->duration_days)->toDateString(),
            'status' => SubscriptionStatus::Pending,
            'auto_renew' => false,
        ])->load(['plan', 'payments']);
    }
}
