<?php

namespace App\Http\Resources;

use App\Enums\SosRequestStatus;
use App\Enums\SosUrgency;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SosRequestResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vehicle' => new VehicleResource($this->whenLoaded('vehicle')),
            'service_type' => new SosServiceTypeResource($this->whenLoaded('serviceType')),
            'assigned_provider' => new SosProviderResource($this->whenLoaded('assignedProvider')),
            'description' => $this->description,
            'image_path' => $this->image_path,
            'image_url' => $this->image_path ? Storage::disk('public')->url($this->image_path) : null,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'urgency' => $this->urgency instanceof SosUrgency ? $this->urgency->value : $this->urgency,
            'status' => $this->status instanceof SosRequestStatus ? $this->status->value : $this->status,
            'accepted_at' => $this->accepted_at?->toISOString(),
            'arrived_at' => $this->arrived_at?->toISOString(),
            'completed_at' => $this->completed_at?->toISOString(),
            'cancelled_at' => $this->cancelled_at?->toISOString(),
            'logs' => SosRequestLogResource::collection($this->whenLoaded('logs')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
