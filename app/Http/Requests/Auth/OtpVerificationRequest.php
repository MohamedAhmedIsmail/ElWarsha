<?php

namespace App\Http\Requests\Auth;

use App\DTOs\Auth\OtpVerificationData;
use App\Enums\OtpPurpose;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class OtpVerificationRequest extends FormRequest
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
            'code' => ['required', 'string', 'size:6'],
            'purpose' => ['required', new Enum(OtpPurpose::class)],
        ];
    }

    public function toDto(): OtpVerificationData
    {
        return new OtpVerificationData(
            phone: $this->validated('phone'),
            code: $this->validated('code'),
            purpose: OtpPurpose::from($this->validated('purpose')),
        );
    }
}
