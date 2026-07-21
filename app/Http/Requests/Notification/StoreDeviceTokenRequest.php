<?php

namespace App\Http\Requests\Notification;

use App\DTOs\Notification\StoreDeviceTokenData;
use App\Enums\DevicePlatform;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreDeviceTokenRequest extends FormRequest
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
            'token' => ['required', 'string'],
            'platform' => ['required', new Enum(DevicePlatform::class)],
            'device_name' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    public function toDto(): StoreDeviceTokenData
    {
        return new StoreDeviceTokenData(
            token: $this->validated('token'),
            platform: DevicePlatform::from($this->validated('platform')),
            deviceName: $this->validated('device_name'),
        );
    }
}
