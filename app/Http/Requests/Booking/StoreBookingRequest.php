<?php

namespace App\Http\Requests\Booking;

use App\DTOs\Booking\StoreBookingData;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
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
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
            'workshop_id' => ['required', 'integer', 'exists:workshops,id'],
            'diagnosis_id' => ['sometimes', 'nullable', 'integer', 'exists:diagnoses,id'],
            'service_id' => ['sometimes', 'nullable', 'integer', 'exists:services,id'],
            'scheduled_at' => ['sometimes', 'nullable', 'date'],
            'description' => ['sometimes', 'nullable', 'string'],
        ];
    }

    public function toDto(): StoreBookingData
    {
        return new StoreBookingData(
            vehicleId: (int) $this->validated('vehicle_id'),
            workshopId: (int) $this->validated('workshop_id'),
            diagnosisId: $this->validated('diagnosis_id'),
            serviceId: $this->validated('service_id'),
            scheduledAt: $this->validated('scheduled_at'),
            description: $this->validated('description'),
        );
    }
}
