<?php

namespace App\Http\Resources;

use App\Enums\ReviewStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'rating' => $this->rating,
            'quality_rating' => $this->quality_rating,
            'price_rating' => $this->price_rating,
            'punctuality_rating' => $this->punctuality_rating,
            'behavior_rating' => $this->behavior_rating,
            'comment' => $this->comment,
            'status' => $this->status instanceof ReviewStatus ? $this->status->value : $this->status,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
