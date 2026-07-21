<?php

namespace App\Repositories\Contracts;

use App\DTOs\Subscription\SubscriptionRequestData;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Workshop;

interface PaymentRepositoryInterface
{
    public function createPending(User $user, Workshop $workshop, Subscription $subscription, Plan $plan, SubscriptionRequestData $data, ?string $receiptPath): Payment;
}
