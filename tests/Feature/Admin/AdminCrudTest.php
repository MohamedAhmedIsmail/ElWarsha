<?php

namespace Tests\Feature\Admin;

use App\Enums\RecordStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Booking;
use App\Models\CarBrand;
use App\Models\CarModel;
use App\Models\Diagnosis;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SosRequest;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
    }

    public function test_admin_can_list_filter_create_update_block_and_soft_delete_users(): void
    {
        User::factory()->create(['name' => 'Target Customer', 'role' => UserRole::Customer, 'status' => UserStatus::Active]);
        User::factory()->create(['name' => 'Other Admin', 'role' => UserRole::Admin, 'status' => UserStatus::Active]);

        $this->actingAs($this->admin)->get('/admin/users?search=Target&role=customer&status=active')
            ->assertOk()
            ->assertSee('Target Customer')
            ->assertDontSee('Other Admin');

        $this->actingAs($this->admin)->post('/admin/users', [
            'name' => 'Manual User',
            'phone' => '01012345678',
            'email' => 'manual@example.com',
            'password' => 'password',
            'role' => UserRole::Customer->value,
            'status' => UserStatus::Active->value,
            'city' => 'Cairo',
            'area' => 'Maadi',
        ])->assertRedirect('/admin/users');

        $user = User::query()->where('phone', '01012345678')->firstOrFail();

        $this->actingAs($this->admin)->put("/admin/users/{$user->id}", [
            'name' => 'Manual User Blocked',
            'phone' => '01012345678',
            'email' => 'manual@example.com',
            'password' => '',
            'role' => UserRole::Customer->value,
            'status' => UserStatus::Blocked->value,
            'city' => 'Cairo',
            'area' => 'Maadi',
        ])->assertRedirect('/admin/users');

        $this->assertSame(UserStatus::Blocked, $user->refresh()->status);

        $this->actingAs($this->admin)->delete("/admin/users/{$user->id}")
            ->assertRedirect('/admin/users');

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_admin_user_details_show_related_records(): void
    {
        $user = User::factory()->create(['name' => 'Customer Details']);
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);
        Booking::factory()->create(['user_id' => $user->id, 'vehicle_id' => $vehicle->id]);
        Diagnosis::factory()->create(['user_id' => $user->id, 'vehicle_id' => $vehicle->id]);
        SosRequest::factory()->create(['user_id' => $user->id, 'vehicle_id' => $vehicle->id]);
        Review::factory()->create(['user_id' => $user->id]);

        $this->actingAs($this->admin)->get("/admin/users/{$user->id}")
            ->assertOk()
            ->assertSee('Customer Details')
            ->assertSee('Vehicles')
            ->assertSee('Bookings')
            ->assertSee('Diagnoses')
            ->assertSee('SOS Requests')
            ->assertSee('Reviews');
    }

    public function test_admin_can_manage_car_brands_and_models(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->post('/admin/car-brands', [
            'name' => 'Toyota',
            'status' => RecordStatus::Active->value,
            'logo' => UploadedFile::fake()->image('toyota.png'),
        ])->assertRedirect('/admin/car-brands');

        $brand = CarBrand::query()->where('name', 'Toyota')->firstOrFail();
        Storage::disk('public')->assertExists($brand->logo);

        $this->actingAs($this->admin)->put("/admin/car-brands/{$brand->id}", [
            'name' => 'Toyota Egypt',
            'status' => RecordStatus::Inactive->value,
        ])->assertRedirect('/admin/car-brands');

        $this->assertSame(RecordStatus::Inactive, $brand->refresh()->status);

        $this->actingAs($this->admin)->post('/admin/car-models', [
            'car_brand_id' => $brand->id,
            'name' => 'Corolla',
            'status' => RecordStatus::Active->value,
        ])->assertRedirect('/admin/car-models');

        $model = CarModel::query()->where('name', 'Corolla')->firstOrFail();

        $this->actingAs($this->admin)->get('/admin/car-models?search=Corolla')
            ->assertOk()
            ->assertSee('Corolla');

        $this->actingAs($this->admin)->put("/admin/car-models/{$model->id}", [
            'car_brand_id' => $brand->id,
            'name' => 'Corolla 2026',
            'status' => RecordStatus::Inactive->value,
        ])->assertRedirect('/admin/car-models');

        $this->assertSame('Corolla 2026', $model->refresh()->name);

        $this->actingAs($this->admin)->delete("/admin/car-models/{$model->id}")->assertRedirect('/admin/car-models');
        $this->assertDatabaseMissing('car_models', ['id' => $model->id]);
    }

    public function test_admin_can_manage_service_categories_and_services(): void
    {
        $this->actingAs($this->admin)->post('/admin/service-categories', [
            'name' => 'Engine Work',
            'slug' => '',
            'icon' => 'wrench',
            'description' => 'Engine related services',
            'sort_order' => 2,
            'status' => RecordStatus::Active->value,
        ])->assertRedirect('/admin/service-categories');

        $category = ServiceCategory::query()->where('slug', 'engine-work')->firstOrFail();

        $this->actingAs($this->admin)->put("/admin/service-categories/{$category->id}", [
            'name' => 'Engine Services',
            'slug' => 'engine-services',
            'icon' => 'wrench',
            'description' => 'Updated',
            'sort_order' => 1,
            'status' => RecordStatus::Inactive->value,
        ])->assertRedirect('/admin/service-categories');

        $this->assertSame(RecordStatus::Inactive, $category->refresh()->status);

        $this->actingAs($this->admin)->post('/admin/services', [
            'service_category_id' => $category->id,
            'name' => 'Oil Change',
            'slug' => '',
            'description' => 'Oil and filter',
            'status' => RecordStatus::Active->value,
        ])->assertRedirect('/admin/services');

        $service = Service::query()->where('slug', 'oil-change')->firstOrFail();

        $this->actingAs($this->admin)->put("/admin/services/{$service->id}", [
            'service_category_id' => $category->id,
            'name' => 'Oil Change Plus',
            'slug' => 'oil-change-plus',
            'description' => 'Updated',
            'status' => RecordStatus::Inactive->value,
        ])->assertRedirect('/admin/services');

        $this->assertSame(RecordStatus::Inactive, $service->refresh()->status);

        $this->actingAs($this->admin)->get('/admin/services?search=Oil')
            ->assertOk()
            ->assertSee('Oil Change Plus');
    }
}
