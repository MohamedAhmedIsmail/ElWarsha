<?php

namespace App\Http\Requests\Vehicle;

use App\DTOs\Vehicle\StoreVehicleData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVehicleRequest extends FormRequest
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
            'car_brand_id' => ['required', 'integer', 'exists:car_brands,id'],
            'car_model_id' => [
                'required',
                'integer',
                Rule::exists('car_models', 'id')->where('car_brand_id', $this->input('car_brand_id')),
            ],
            'year' => ['sometimes', 'nullable', 'integer', 'digits:4', 'min:1900', 'max:' . ((int) date('Y') + 1)],
            'mileage_km' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:2000000'],
            'plate_number' => ['sometimes', 'nullable', 'string', 'max:50'],
            'vin' => ['sometimes', 'nullable', 'string', 'max:100'],
            'color' => ['sometimes', 'nullable', 'string', 'max:50'],
            'image' => ['sometimes', 'nullable', 'string', 'max:255'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }

    public function toDto(): StoreVehicleData
    {
        return new StoreVehicleData(
            carBrandId: (int) $this->validated('car_brand_id'),
            carModelId: (int) $this->validated('car_model_id'),
            year: $this->validated('year'),
            mileageKm: $this->validated('mileage_km'),
            plateNumber: $this->validated('plate_number'),
            vin: $this->validated('vin'),
            color: $this->validated('color'),
            image: $this->validated('image'),
            notes: $this->validated('notes'),
        );
    }
}
