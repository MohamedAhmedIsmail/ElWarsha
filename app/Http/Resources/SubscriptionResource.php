<?php

namespace App\Http\Resources;

use App\Enums\SubscriptionStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'workshop_id' => $this->workshop_id,
            'plan' => new PlanResource($this->whenLoaded('plan')),
            'plan_id' => $this->plan_id,
            'starts_at' => $this->starts_at?->toDateString(),
            'ends_at' => $this->ends_at?->toDateString(),
            'status' => $this->status instanceof SubscriptionStatus ? $this->status->value : $this->status,
            'auto_renew' => $this->auto_renew,
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
