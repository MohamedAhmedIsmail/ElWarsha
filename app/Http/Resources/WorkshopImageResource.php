<?php

namespace App\Http\Resources;

use App\Enums\WorkshopImageType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class WorkshopImageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'image_path' => $this->image_path,
            'url' => Storage::disk('public')->url($this->image_path),
            'type' => $this->type instanceof WorkshopImageType ? $this->type->value : $this->type,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
