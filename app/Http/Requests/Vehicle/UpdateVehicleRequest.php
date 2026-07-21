<?php

namespace App\Http\Requests\Vehicle;

use App\DTOs\Vehicle\UpdateVehicleData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehicleRequest extends FormRequest
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
        $brandId = $this->input('car_brand_id');

        return [
            'car_brand_id' => ['sometimes', 'integer', 'exists:car_brands,id'],
            'car_model_id' => [
                'sometimes',
                'integer',
                $brandId
                    ? Rule::exists('car_models', 'id')->where('car_brand_id', $brandId)
                    : Rule::exists('car_models', 'id'),
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

    public function toDto(): UpdateVehicleData
    {
        return new UpdateVehicleData($this->validated());
    }
}
