<?php

namespace App\Http\Requests\ServiceLedger;

use App\DTOs\ServiceLedger\UploadServiceLedgerMediaData;
use App\Enums\ServiceLedgerMediaType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UploadServiceLedgerMediaRequest extends FormRequest
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
            'media_type' => ['required', new Enum(ServiceLedgerMediaType::class)],
            'files' => ['required', 'array', 'min:1', 'max:5'],
            'files.*' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    public function toDto(): UploadServiceLedgerMediaData
    {
        return new UploadServiceLedgerMediaData(
            mediaType: ServiceLedgerMediaType::from($this->validated('media_type')),
            files: $this->file('files'),
        );
    }
}
