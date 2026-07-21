<?php

namespace App\Http\Resources;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'workshop_id' => $this->workshop_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'vehicle' => new VehicleResource($this->whenLoaded('vehicle')),
            'vehicle_id' => $this->vehicle_id,
            'booking_id' => $this->booking_id,
            'diagnosis_id' => $this->diagnosis_id,
            'sos_request_id' => $this->sos_request_id,
            'source' => $this->source instanceof LeadSource ? $this->source->value : $this->source,
            'status' => $this->status instanceof LeadStatus ? $this->status->value : $this->status,
            'notes' => $this->notes,
            'lead_notes' => LeadNoteResource::collection($this->whenLoaded('leadNotes')),
            'status_logs' => LeadStatusLogResource::collection($this->whenLoaded('statusLogs')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
