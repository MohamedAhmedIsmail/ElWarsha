<?php

namespace App\Services;

use App\DTOs\Subscription\SubscriptionRequestData;
use App\Enums\UserRole;
use App\Models\Subscription;
use App\Models\User;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\PlanRepositoryInterface;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use App\Repositories\Contracts\WorkshopRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WorkshopSubscriptionService
{
    public function __construct(
        private readonly WorkshopRepositoryInterface $workshops,
        private readonly PlanRepositoryInterface $plans,
        private readonly SubscriptionRepositoryInterface $subscriptions,
        private readonly PaymentRepositoryInterface $payments,
    ) {
    }

    public function current(User $owner): ?Subscription
    {
        $workshop = $this->getOwnedWorkshop($owner);

        return $this->subscriptions->latestForWorkshop($workshop);
    }

    public function request(User $owner, SubscriptionRequestData $data): Subscription
    {
        $workshop = $this->getOwnedWorkshop($owner);
        $plan = $this->plans->findActive($data->planId)
            ?? throw ValidationException::withMessages(['plan_id' => __('The selected plan was not found.')]);
        $receiptPath = $data->receiptImage?->store("subscriptions/{$workshop->id}/receipts", 'public');

        return DB::transaction(function () use ($owner, $workshop, $plan, $data, $receiptPath): Subscription {
            $subscription = $this->subscriptions->createPending($workshop, $plan);
            $this->payments->createPending($owner, $workshop, $subscription, $plan, $data, $receiptPath);

            return $subscription->refresh()->load(['plan', 'payments']);
        });
    }

    private function getOwnedWorkshop(User $owner)
    {
        if ($owner->role !== UserRole::WorkshopOwner) {
            throw new NotFoundHttpException('Workshop profile not found.');
        }

        return $this->workshops->findForOwner($owner->id)
            ?? throw new NotFoundHttpException('Workshop profile not found.');
    }
}
