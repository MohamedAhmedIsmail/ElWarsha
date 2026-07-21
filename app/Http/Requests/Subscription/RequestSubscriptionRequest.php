<?php

namespace App\Http\Requests\Subscription;

use App\DTOs\Subscription\SubscriptionRequestData;
use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class RequestSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
            'payment_method' => ['required', new Enum(PaymentMethod::class)],
            'transaction_reference' => ['sometimes', 'nullable', 'string', 'max:255'],
            'receipt_image' => ['sometimes', 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    public function toDto(): SubscriptionRequestData
    {
        return new SubscriptionRequestData(
            planId: (int) $this->validated('plan_id'),
            paymentMethod: PaymentMethod::from($this->validated('payment_method')),
            transactionReference: $this->validated('transaction_reference'),
            receiptImage: $this->file('receipt_image'),
        );
    }
}
