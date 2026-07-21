<?php

namespace Database\Factories;

use App\Enums\RecordStatus;
use App\Models\CarBrand;
use App\Models\CarModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $brand = CarBrand::factory()->create();
        $model = CarModel::factory()->create(['car_brand_id' => $brand->id]);

        return [
            'user_id' => User::factory(),
            'car_brand_id' => $brand->id,
            'car_model_id' => $model->id,
            'year' => fake()->numberBetween(2000, (int) date('Y')),
            'mileage_km' => fake()->numberBetween(0, 300000),
            'plate_number' => fake()->optional()->bothify('???-####'),
            'vin' => fake()->optional()->bothify('?????????????????'),
            'color' => fake()->safeColorName(),
            'image' => fake()->optional()->imageUrl(),
            'notes' => fake()->optional()->sentence(),
            'status' => RecordStatus::Active,
        ];
    }
}
