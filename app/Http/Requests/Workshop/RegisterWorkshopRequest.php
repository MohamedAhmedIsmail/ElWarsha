<?php

namespace App\Http\Requests\Workshop;

use App\DTOs\Workshop\WorkshopData;

class RegisterWorkshopRequest extends WorkshopOwnerRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'phone' => ['required', 'string', 'max:30'],
            'whatsapp' => ['sometimes', 'nullable', 'string', 'max:30'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string', 'max:100'],
            'area' => ['required', 'string', 'max:100'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
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
