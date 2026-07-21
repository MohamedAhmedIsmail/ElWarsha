<?php

namespace App\Http\Resources;

use App\Enums\RecordStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceItemResource extends JsonResource
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
            'default_interval_km' => $this->default_interval_km,
            'default_interval_months' => $this->default_interval_months,
            'service_category' => new ServiceCategoryResource($this->whenLoaded('serviceCategory')),
            'status' => $this->status instanceof RecordStatus ? $this->status->value : $this->status,
        ];
    }
}
