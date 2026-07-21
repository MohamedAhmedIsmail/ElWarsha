<?php

namespace Tests\Feature;

use App\Models\CarBrand;
use App\Models\CarModel;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_vehicle_endpoints(): void
    {
        $this->getJson('/api/vehicles')
            ->assertUnauthorized()
            ->assertJsonPath('success', false);
    }

    public function test_user_can_list_only_own_vehicles(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $ownVehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        Vehicle::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/vehicles');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.id', $ownVehicle->id)
            ->assertJsonStructure(['data' => ['items' => [['brand', 'model']]]]);
    }

    public function test_user_can_create_vehicle(): void
    {
        $user = User::factory()->create();
        $brand = CarBrand::factory()->create(['name' => 'Toyota']);
        $model = CarModel::factory()->create(['car_brand_id' => $brand->id, 'name' => 'Corolla']);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/vehicles', [
            'car_brand_id' => $brand->id,
            'car_model_id' => $model->id,
            'year' => 2020,
            'mileage_km' => 65000,
            'plate_number' => 'ABC-1234',
            'color' => 'Black',
            'notes' => 'Family car',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.vehicle.brand.name', 'Toyota')
            ->assertJsonPath('data.vehicle.model.name', 'Corolla');

        $this->assertDatabaseHas('vehicles', [
            'user_id' => $user->id,
            'car_brand_id' => $brand->id,
            'car_model_id' => $model->id,
            'year' => 2020,
        ]);
    }

    public function test_create_vehicle_requires_model_to_belong_to_brand(): void
    {
        $user = User::factory()->create();
        $brand = CarBrand::factory()->create();
        $otherBrand = CarBrand::factory()->create();
        $otherModel = CarModel::factory()->create(['car_brand_id' => $otherBrand->id]);

        $this->actingAs($user, 'sanctum')->postJson('/api/vehicles', [
            'car_brand_id' => $brand->id,
            'car_model_id' => $otherModel->id,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['car_model_id']);
    }

    public function test_user_can_show_and_update_own_vehicle(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user, 'sanctum')->getJson("/api/vehicles/{$vehicle->id}")
            ->assertOk()
            ->assertJsonPath('data.vehicle.id', $vehicle->id);

        $this->actingAs($user, 'sanctum')->putJson("/api/vehicles/{$vehicle->id}", [
            'mileage_km' => 90000,
            'color' => 'Blue',
        ])
            ->assertOk()
            ->assertJsonPath('data.vehicle.mileage_km', 90000)
            ->assertJsonPath('data.vehicle.color', 'Blue');
    }

    public function test_user_cannot_access_another_users_vehicle(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user, 'sanctum')->getJson("/api/vehicles/{$vehicle->id}")
            ->assertNotFound()
            ->assertJsonPath('success', false);

        $this->actingAs($user, 'sanctum')->putJson("/api/vehicles/{$vehicle->id}", [
            'color' => 'Red',
        ])->assertNotFound();
    }

    public function test_user_can_soft_delete_own_vehicle(): void
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user, 'sanctum')->deleteJson("/api/vehicles/{$vehicle->id}")
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSoftDeleted('vehicles', ['id' => $vehicle->id]);
    }
}
