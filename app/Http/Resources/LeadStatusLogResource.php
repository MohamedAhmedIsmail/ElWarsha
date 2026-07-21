<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadStatusLogResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'old_status' => $this->old_status,
            'new_status' => $this->new_status,
            'changed_by' => new UserResource($this->whenLoaded('changedBy')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
