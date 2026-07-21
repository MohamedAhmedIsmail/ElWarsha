<?php

namespace App\Http\Resources;

use App\Enums\SosUrgency;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmergencyGuidanceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vehicle' => new VehicleResource($this->whenLoaded('vehicle')),
            'description' => $this->description,
            'symptoms' => $this->symptoms_json,
            'urgency' => $this->urgency instanceof SosUrgency ? $this->urgency->value : $this->urgency,
            'needs_sos' => $this->needs_sos,
            'recommended_sos_service_type' => new SosServiceTypeResource($this->whenLoaded('recommendedSosServiceType')),
            'safety_message' => $this->safety_message,
            'guidance' => $this->ai_response,
            'location' => [
                'latitude' => $this->latitude === null ? null : (float) $this->latitude,
                'longitude' => $this->longitude === null ? null : (float) $this->longitude,
            ],
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
