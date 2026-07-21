<?php

namespace App\Http\Requests\Auth;

use App\DTOs\Auth\OtpRequestData;
use App\Enums\OtpPurpose;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class OtpRequest extends FormRequest
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
            'phone' => ['required', 'string', 'max:30'],
            'purpose' => ['required', new Enum(OtpPurpose::class)],
        ];
    }

    public function toDto(): OtpRequestData
    {
        return new OtpRequestData(
            phone: $this->validated('phone'),
            purpose: OtpPurpose::from($this->validated('purpose')),
        );
    }
}
