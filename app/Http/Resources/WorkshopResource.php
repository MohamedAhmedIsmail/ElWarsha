<?php

namespace App\Http\Resources;

use App\Enums\WorkshopStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkshopResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'email' => $this->email,
            'address' => $this->address,
            'city' => $this->city,
            'area' => $this->area,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'distance_km' => $this->when(isset($this->distance), fn () => round((float) $this->distance, 2)),
            'google_maps_url' => $this->google_maps_url,
            'accepts_booking' => $this->accepts_booking,
            'accepts_sos' => $this->accepts_sos,
            'is_verified' => $this->is_verified,
            'rating_avg' => $this->rating_avg,
            'reviews_count' => $this->reviews_count,
            'status' => $this->status instanceof WorkshopStatus ? $this->status->value : $this->status,
            'services' => ServiceResource::collection($this->whenLoaded('services')),
            'brands' => CarBrandResource::collection($this->whenLoaded('brands')),
            'images' => WorkshopImageResource::collection($this->whenLoaded('images')),
            'working_hours' => WorkshopWorkingHourResource::collection($this->whenLoaded('workingHours')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
