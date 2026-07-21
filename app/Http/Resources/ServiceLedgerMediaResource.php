<?php

namespace App\Http\Resources;

use App\Enums\ServiceLedgerMediaType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceLedgerMediaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'media_type' => $this->media_type instanceof ServiceLedgerMediaType ? $this->media_type->value : $this->media_type,
            'file_path' => $this->file_path,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
