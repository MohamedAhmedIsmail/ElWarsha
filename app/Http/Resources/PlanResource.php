<?php

namespace App\Http\Resources;

use App\Enums\RecordStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'price' => $this->price,
            'duration_days' => $this->duration_days,
            'description' => $this->description,
            'features' => $this->features,
            'is_featured' => $this->is_featured,
            'status' => $this->status instanceof RecordStatus ? $this->status->value : $this->status,
        ];
    }
}
