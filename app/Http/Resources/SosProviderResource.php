<?php

namespace App\Http\Resources;

use App\Enums\WorkshopStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SosProviderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'city' => $this->city,
            'area' => $this->area,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'distance_km' => $this->when(isset($this->distance), fn () => round((float) $this->distance, 2)),
            'is_available' => $this->is_available,
            'rating_avg' => $this->rating_avg,
            'status' => $this->status instanceof WorkshopStatus ? $this->status->value : $this->status,
            'workshop' => new WorkshopResource($this->whenLoaded('workshop')),
            'service_types' => SosServiceTypeResource::collection($this->whenLoaded('serviceTypes')),
        ];
    }
}
