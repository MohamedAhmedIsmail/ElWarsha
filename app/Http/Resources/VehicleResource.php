<?php

namespace App\Http\Resources;

use App\Enums\RecordStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'car_brand_id' => $this->car_brand_id,
            'car_model_id' => $this->car_model_id,
            'year' => $this->year,
            'mileage_km' => $this->mileage_km,
            'plate_number' => $this->plate_number,
            'vin' => $this->vin,
            'color' => $this->color,
            'image' => $this->image,
            'notes' => $this->notes,
            'status' => $this->status instanceof RecordStatus ? $this->status->value : $this->status,
            'brand' => new CarBrandResource($this->whenLoaded('brand')),
            'model' => new CarModelResource($this->whenLoaded('model')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
