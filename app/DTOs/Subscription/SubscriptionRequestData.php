<?php

namespace App\DTOs\Subscription;

use App\Enums\PaymentMethod;
use Illuminate\Http\UploadedFile;

class SubscriptionRequestData
{
    public function __construct(
        public readonly int $planId,
        public readonly PaymentMethod $paymentMethod,
        public readonly ?string $transactionReference,
        public readonly ?UploadedFile $receiptImage,
    ) {
    }
}
