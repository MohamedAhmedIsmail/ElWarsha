<?php

namespace App\Http\Requests\Workshop;

use App\DTOs\Workshop\WorkshopFilterData;
use Illuminate\Foundation\Http\FormRequest;

class WorkshopIndexRequest extends FormRequest
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
            'service_id' => ['sometimes', 'integer', 'exists:services,id'],
            'category_id' => ['sometimes', 'integer', 'exists:service_categories,id'],
            'brand_id' => ['sometimes', 'integer', 'exists:car_brands,id'],
            'city' => ['sometimes', 'nullable', 'string', 'max:100'],
            'area' => ['sometimes', 'nullable', 'string', 'max:100'],
            'lat' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'lng' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'radius' => ['sometimes', 'nullable', 'numeric', 'min:1', 'max:500'],
            'rating' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:5'],
            'is_verified' => ['sometimes', 'boolean'],
            'open_now' => ['sometimes', 'boolean'],
            'accepts_booking' => ['sometimes', 'boolean'],
            'accepts_sos' => ['sometimes', 'boolean'],
            'search' => ['sometimes', 'nullable', 'string', 'max:100'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function toDto(): WorkshopFilterData
    {
        return new WorkshopFilterData(
            serviceId: $this->validated('service_id'),
            categoryId: $this->validated('category_id'),
            brandId: $this->validated('brand_id'),
            city: $this->validated('city'),
            area: $this->validated('area'),
            lat: $this->validated('lat') !== null ? (float) $this->validated('lat') : null,
            lng: $this->validated('lng') !== null ? (float) $this->validated('lng') : null,
            radius: (float) ($this->validated('radius') ?? 10),
            rating: $this->validated('rating') !== null ? (float) $this->validated('rating') : null,
            isVerified: $this->has('is_verified') ? $this->boolean('is_verified') : null,
            openNow: $this->has('open_now') ? $this->boolean('open_now') : null,
            acceptsBooking: $this->has('accepts_booking') ? $this->boolean('accepts_booking') : null,
            acceptsSos: $this->has('accepts_sos') ? $this->boolean('accepts_sos') : null,
            search: $this->validated('search'),
            perPage: (int) ($this->validated('per_page') ?? 15),
        );
    }
}
