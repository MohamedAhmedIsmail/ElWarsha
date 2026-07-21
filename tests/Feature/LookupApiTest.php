<?php

namespace Tests\Feature;

use App\Models\CarBrand;
use App\Models\CarModel;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LookupApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_car_brands_return_only_active_records_and_can_search(): void
    {
        CarBrand::factory()->create(['name' => 'Toyota']);
        CarBrand::factory()->create(['name' => 'Honda']);
        CarBrand::factory()->inactive()->create(['name' => 'Toyota Hidden']);

        $response = $this->getJson('/api/car-brands?search=Toy');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.name', 'Toyota');
    }

    public function test_car_brand_models_return_only_active_models_for_active_brand(): void
    {
        $brand = CarBrand::factory()->create(['name' => 'Toyota']);
        $otherBrand = CarBrand::factory()->create(['name' => 'Honda']);

        CarModel::factory()->create(['car_brand_id' => $brand->id, 'name' => 'Corolla']);
        CarModel::factory()->inactive()->create(['car_brand_id' => $brand->id, 'name' => 'Hidden']);
        CarModel::factory()->create(['car_brand_id' => $otherBrand->id, 'name' => 'Civic']);

        $response = $this->getJson("/api/car-brands/{$brand->id}/models");

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.name', 'Corolla');
    }

    public function test_car_models_support_optional_pagination(): void
    {
        CarModel::factory()->count(3)->create();

        $response = $this->getJson('/api/car-models?per_page=2');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data.items')
            ->assertJsonPath('data.meta.per_page', 2)
            ->assertJsonPath('data.meta.total', 3);
    }

    public function test_service_categories_return_active_records_sorted_by_sort_order(): void
    {
        ServiceCategory::factory()->create(['name' => 'Second', 'slug' => 'second', 'sort_order' => 20]);
        ServiceCategory::factory()->create(['name' => 'First', 'slug' => 'first', 'sort_order' => 10]);
        ServiceCategory::factory()->inactive()->create(['name' => 'Hidden', 'slug' => 'hidden', 'sort_order' => 1]);

        $response = $this->getJson('/api/service-categories');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data.items')
            ->assertJsonPath('data.items.0.name', 'First')
            ->assertJsonPath('data.items.1.name', 'Second');
    }

    public function test_services_return_active_records_for_active_categories_and_can_search(): void
    {
        $category = ServiceCategory::factory()->create(['name' => 'Engine', 'slug' => 'engine', 'sort_order' => 1]);
        $inactiveCategory = ServiceCategory::factory()->inactive()->create(['name' => 'Hidden Cat', 'slug' => 'hidden-cat']);

        Service::factory()->create(['service_category_id' => $category->id, 'name' => 'Oil Change', 'slug' => 'oil-change']);
        Service::factory()->create(['service_category_id' => $category->id, 'name' => 'Battery Check', 'slug' => 'battery-check']);
        Service::factory()->inactive()->create(['service_category_id' => $category->id, 'name' => 'Oil Hidden', 'slug' => 'oil-hidden']);
        Service::factory()->create(['service_category_id' => $inactiveCategory->id, 'name' => 'Oil Inactive Category', 'slug' => 'oil-inactive-category']);

        $response = $this->getJson('/api/services?search=Oil');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.name', 'Oil Change');
    }

    public function test_service_category_services_return_only_category_services(): void
    {
        $category = ServiceCategory::factory()->create(['name' => 'Engine', 'slug' => 'engine']);
        $otherCategory = ServiceCategory::factory()->create(['name' => 'Tires', 'slug' => 'tires']);

        Service::factory()->create(['service_category_id' => $category->id, 'name' => 'Oil Change', 'slug' => 'oil-change']);
        Service::factory()->create(['service_category_id' => $otherCategory->id, 'name' => 'Tire Repair', 'slug' => 'tire-repair']);

        $response = $this->getJson("/api/service-categories/{$category->id}/services");

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.name', 'Oil Change');
    }
}
