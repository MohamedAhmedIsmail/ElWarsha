<?php

namespace App\Http\Requests\Diagnosis;

use App\DTOs\Diagnosis\DiagnosisSuggestionData;
use Illuminate\Foundation\Http\FormRequest;

class DiagnosisSuggestionsRequest extends FormRequest
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
            'lat' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'lng' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function toDto(): DiagnosisSuggestionData
    {
        return new DiagnosisSuggestionData(
            lat: $this->validated('lat') !== null ? (float) $this->validated('lat') : null,
            lng: $this->validated('lng') !== null ? (float) $this->validated('lng') : null,
            limit: (int) ($this->validated('limit') ?? 10),
        );
    }
}
