<?php

namespace App\Http\Resources;

use App\Enums\DiagnosisConfidence;
use App\Enums\DiagnosisStatus;
use App\Enums\DiagnosisUrgency;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiagnosisResource extends JsonResource
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
            'symptoms_json' => $this->symptoms_json,
            'ai_response' => $this->ai_response,
            'diagnosis_text' => $this->diagnosis_text,
            'confidence' => $this->confidence instanceof DiagnosisConfidence ? $this->confidence->value : $this->confidence,
            'urgency' => $this->urgency instanceof DiagnosisUrgency ? $this->urgency->value : $this->urgency,
            'affected_category' => new ServiceCategoryResource($this->whenLoaded('affectedCategory')),
            'recommend_professional' => $this->recommend_professional,
            'status' => $this->status instanceof DiagnosisStatus ? $this->status->value : $this->status,
            'disclaimer_accepted' => $this->disclaimer_accepted,
            'media' => DiagnosisMediaResource::collection($this->whenLoaded('media')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
