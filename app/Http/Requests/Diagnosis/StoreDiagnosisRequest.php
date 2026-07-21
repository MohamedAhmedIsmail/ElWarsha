<?php

namespace App\Http\Requests\Diagnosis;

use App\DTOs\Diagnosis\StoreDiagnosisData;
use Illuminate\Foundation\Http\FormRequest;

class StoreDiagnosisRequest extends FormRequest
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
            'description' => ['required', 'string', 'min:5'],
            'symptoms_json' => ['sometimes', 'nullable', 'array'],
            'disclaimer_accepted' => ['sometimes', 'boolean'],
        ];
    }

    public function toDto(): StoreDiagnosisData
    {
        return new StoreDiagnosisData(
            vehicleId: (int) $this->validated('vehicle_id'),
            description: $this->validated('description'),
            symptomsJson: $this->validated('symptoms_json'),
            disclaimerAccepted: $this->boolean('disclaimer_accepted'),
        );
    }
}
