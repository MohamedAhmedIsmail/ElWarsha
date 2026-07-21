<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Subscription\SubscriptionRequestData;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Workshop;
use App\Repositories\Contracts\PaymentRepositoryInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function createPending(User $user, Workshop $workshop, Subscription $subscription, Plan $plan, SubscriptionRequestData $data, ?string $receiptPath): Payment
    {
        return Payment::query()->create([
            'user_id' => $user->id,
            'workshop_id' => $workshop->id,
            'subscription_id' => $subscription->id,
            'amount' => $plan->price,
            'payment_method' => $data->paymentMethod,
            'transaction_reference' => $data->transactionReference,
            'receipt_image' => $receiptPath,
            'status' => PaymentStatus::Pending,
        ])->load(['subscription.plan']);
    }
}
