<?php

namespace App\Http\Requests\Crm;

use App\DTOs\Crm\TrackWorkshopEventData;
use Illuminate\Foundation\Http\FormRequest;

class TrackWorkshopEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'vehicle_id' => ['sometimes', 'nullable', 'integer', 'exists:vehicles,id'],
            'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'metadata' => ['sometimes', 'nullable', 'array'],
        ];
    }

    public function toDto(): TrackWorkshopEventData
    {
        return new TrackWorkshopEventData(
            vehicleId: $this->validated('vehicle_id') === null ? null : (int) $this->validated('vehicle_id'),
            latitude: $this->validated('latitude') === null ? null : (float) $this->validated('latitude'),
            longitude: $this->validated('longitude') === null ? null : (float) $this->validated('longitude'),
            metadata: $this->validated('metadata'),
        );
    }
}
