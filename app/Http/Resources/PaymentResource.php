<?php

namespace App\Http\Resources;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'workshop_id' => $this->workshop_id,
            'subscription_id' => $this->subscription_id,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method instanceof PaymentMethod ? $this->payment_method->value : $this->payment_method,
            'transaction_reference' => $this->transaction_reference,
            'receipt_image' => $this->receipt_image,
            'status' => $this->status instanceof PaymentStatus ? $this->status->value : $this->status,
            'approved_at' => $this->approved_at?->toISOString(),
            'admin_notes' => $this->admin_notes,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
