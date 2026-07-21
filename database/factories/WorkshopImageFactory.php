<?php

namespace Database\Factories;

use App\Enums\WorkshopImageType;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkshopImage>
 */
class WorkshopImageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workshop_id' => Workshop::factory(),
            'image_path' => 'workshops/example.jpg',
            'type' => WorkshopImageType::Workshop,
            'sort_order' => 0,
        ];
    }
}
