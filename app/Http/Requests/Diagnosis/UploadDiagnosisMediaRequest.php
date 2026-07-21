<?php

namespace App\Http\Requests\Diagnosis;

use App\DTOs\Diagnosis\UploadDiagnosisMediaData;
use App\Enums\DiagnosisMediaType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UploadDiagnosisMediaRequest extends FormRequest
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
            'media_type' => ['required', new Enum(DiagnosisMediaType::class)],
            'files' => ['required', 'array', 'min:1', 'max:10'],
            'files.*' => ['required', 'file', 'max:10240'],
        ];
    }

    public function toDto(): UploadDiagnosisMediaData
    {
        return new UploadDiagnosisMediaData(
            files: $this->file('files', []),
            mediaType: DiagnosisMediaType::from($this->validated('media_type')),
        );
    }
}
