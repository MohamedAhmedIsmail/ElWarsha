<?php

namespace App\Http\Resources;

use App\Enums\RecordStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarModelResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'car_brand_id' => $this->car_brand_id,
            'name' => $this->name,
            'status' => $this->status instanceof RecordStatus ? $this->status->value : $this->status,
            'brand' => new CarBrandResource($this->whenLoaded('brand')),
        ];
    }
}
