<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiagnosisWorkshopSuggestionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'score' => $this->score,
            'reason' => $this->reason,
            'workshop' => new WorkshopResource($this->whenLoaded('workshop')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
