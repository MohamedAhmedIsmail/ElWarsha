<?php

namespace App\Http\Requests\Workshop;

use App\DTOs\Workshop\WorkshopData;

class UpdateWorkshopProfileRequest extends WorkshopOwnerRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'phone' => ['sometimes', 'string', 'max:30'],
            'whatsapp' => ['sometimes', 'nullable', 'string', 'max:30'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'address' => ['sometimes', 'string'],
            'city' => ['sometimes', 'string', 'max:100'],
            'area' => ['sometimes', 'string', 'max:100'],
            'latitude' => ['sometimes', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'numeric', 'between:-180,180'],
            'google_maps_url' => ['sometimes', 'nullable', 'string'],
            'accepts_booking' => ['sometimes', 'boolean'],
            'accepts_sos' => ['sometimes', 'boolean'],
        ];
    }

    public function toDto(): WorkshopData
    {
        return new WorkshopData($this->validated());
    }
}
