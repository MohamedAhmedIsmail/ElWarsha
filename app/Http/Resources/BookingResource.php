<?php

namespace App\Http\Resources;

use App\Enums\BookingStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vehicle' => new VehicleResource($this->whenLoaded('vehicle')),
            'workshop' => new WorkshopResource($this->whenLoaded('workshop')),
            'diagnosis' => new DiagnosisResource($this->whenLoaded('diagnosis')),
            'service' => new ServiceResource($this->whenLoaded('service')),
            'scheduled_at' => $this->scheduled_at?->toISOString(),
            'description' => $this->description,
            'status' => $this->status instanceof BookingStatus ? $this->status->value : $this->status,
            'workshop_notes' => $this->workshop_notes,
            'completed_at' => $this->completed_at?->toISOString(),
            'cancelled_at' => $this->cancelled_at?->toISOString(),
            'status_logs' => BookingStatusLogResource::collection($this->whenLoaded('statusLogs')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
