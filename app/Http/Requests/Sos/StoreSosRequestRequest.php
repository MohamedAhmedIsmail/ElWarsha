<?php

namespace App\Http\Requests\Sos;

use App\DTOs\Sos\StoreSosRequestData;
use App\Enums\SosUrgency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreSosRequestRequest extends FormRequest
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
            'sos_service_type_id' => ['required', 'integer', 'exists:sos_service_types,id'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'vehicle_id' => ['sometimes', 'nullable', 'integer', 'exists:vehicles,id'],
            'description' => ['sometimes', 'nullable', 'string'],
            'image' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'urgency' => ['sometimes', new Enum(SosUrgency::class)],
        ];
    }

    public function toDto(): StoreSosRequestData
    {
        return new StoreSosRequestData(
            sosServiceTypeId: (int) $this->validated('sos_service_type_id'),
            latitude: (float) $this->validated('latitude'),
            longitude: (float) $this->validated('longitude'),
            vehicleId: $this->validated('vehicle_id'),
            description: $this->validated('description'),
            image: $this->file('image'),
            urgency: SosUrgency::from($this->validated('urgency') ?? SosUrgency::Medium->value),
        );
    }
}
