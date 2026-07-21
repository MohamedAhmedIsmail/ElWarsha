<?php

namespace App\Http\Requests\EmergencyGuidance;

use App\DTOs\EmergencyGuidance\StoreEmergencyGuidanceData;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmergencyGuidanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'vehicle_id' => ['sometimes', 'nullable', 'integer', 'exists:vehicles,id'],
            'description' => ['required', 'string', 'min:5'],
            'symptoms' => ['sometimes', 'nullable', 'array'],
            'symptoms.*' => ['string', 'max:100'],
            'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
        ];
    }

    public function toDto(): StoreEmergencyGuidanceData
    {
        return new StoreEmergencyGuidanceData(
            vehicleId: $this->validated('vehicle_id') === null ? null : (int) $this->validated('vehicle_id'),
            description: $this->validated('description'),
            symptoms: $this->validated('symptoms'),
            latitude: $this->validated('latitude') === null ? null : (float) $this->validated('latitude'),
            longitude: $this->validated('longitude') === null ? null : (float) $this->validated('longitude'),
        );
    }
}
