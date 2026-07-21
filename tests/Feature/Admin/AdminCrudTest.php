<?php

namespace Tests\Feature\Admin;

use App\Enums\RecordStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\BookingStatus;
use App\Enums\DiagnosisConfidence;
use App\Enums\DiagnosisStatus;
use App\Enums\DiagnosisUrgency;
use App\Enums\WorkshopStatus;
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
use App\Models\Workshop;
use App\Models\WorkshopVerification;
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

    public function test_admin_can_manage_workshops(): void
    {
        $owner = User::factory()->create(['role' => UserRole::WorkshopOwner]);
        $service = Service::factory()->create(['name' => 'Battery Check']);
        $brand = CarBrand::factory()->create(['name' => 'Nissan']);

        $this->actingAs($this->admin)->post('/admin/workshops', [
            'owner_id' => $owner->id,
            'name' => 'Downtown Workshop',
            'description' => 'General repairs',
            'phone' => '01099999999',
            'whatsapp' => '01099999999',
            'email' => 'workshop@example.com',
            'address' => '10 Street',
            'city' => 'Cairo',
            'area' => 'Dokki',
            'latitude' => 30.0444,
            'longitude' => 31.2357,
            'accepts_booking' => 1,
            'accepts_sos' => 1,
            'status' => WorkshopStatus::Pending->value,
            'subscription_status' => 'free',
            'service_ids' => [$service->id],
            'brand_ids' => [$brand->id],
            'hours' => [
                'monday' => ['opens_at' => '09:00', 'closes_at' => '18:00'],
            ],
        ])->assertRedirect();

        $workshop = Workshop::query()->where('name', 'Downtown Workshop')->firstOrFail();

        $this->assertTrue($workshop->services()->whereKey($service->id)->exists());
        $this->assertTrue($workshop->brands()->whereKey($brand->id)->exists());
        $this->assertDatabaseHas('workshop_working_hours', ['workshop_id' => $workshop->id, 'day_of_week' => 'monday']);

        $this->actingAs($this->admin)->get('/admin/workshops?status=pending&city=Cairo&area=Dokki&accepts_booking=1')
            ->assertOk()
            ->assertSee('Downtown Workshop');

        $this->actingAs($this->admin)->post("/admin/workshops/{$workshop->id}/approve")->assertRedirect();
        $this->assertSame(WorkshopStatus::Approved, $workshop->refresh()->status);

        $this->actingAs($this->admin)->post("/admin/workshops/{$workshop->id}/verify")->assertRedirect();
        $this->assertTrue($workshop->refresh()->is_verified);

        $this->actingAs($this->admin)->post("/admin/workshops/{$workshop->id}/unverify")->assertRedirect();
        $this->assertFalse($workshop->refresh()->is_verified);
    }

    public function test_admin_can_approve_and_reject_workshop_verifications(): void
    {
        $workshop = Workshop::factory()->create(['is_verified' => false]);
        $verification = WorkshopVerification::query()->create([
            'workshop_id' => $workshop->id,
            'commercial_register' => 'docs/cr.pdf',
            'tax_card' => 'docs/tax.pdf',
            'owner_id_image' => 'docs/id.jpg',
            'workshop_license' => 'docs/license.pdf',
            'status' => 'pending',
        ]);

        $this->actingAs($this->admin)->get("/admin/workshop-verifications/{$verification->id}")
            ->assertOk()
            ->assertSee('Commercial Register')
            ->assertSee('Workshop License');

        $this->actingAs($this->admin)->post("/admin/workshop-verifications/{$verification->id}/approve", [
            'admin_notes' => 'Documents are valid.',
        ])->assertRedirect();

        $this->assertSame('approved', $verification->refresh()->status);
        $this->assertSame($this->admin->id, $verification->verified_by);
        $this->assertNotNull($verification->verified_at);
        $this->assertTrue($workshop->refresh()->is_verified);

        $second = WorkshopVerification::query()->create([
            'workshop_id' => $workshop->id,
            'status' => 'pending',
        ]);

        $this->actingAs($this->admin)->post("/admin/workshop-verifications/{$second->id}/reject", [
            'admin_notes' => 'Missing tax card.',
        ])->assertRedirect();

        $this->assertSame('rejected', $second->refresh()->status);
        $this->assertSame('Missing tax card.', $second->admin_notes);
    }

    public function test_admin_can_filter_and_update_diagnosis_status(): void
    {
        $category = ServiceCategory::factory()->create(['name' => 'Electricity']);
        $diagnosis = Diagnosis::factory()->completed($category)->create([
            'status' => DiagnosisStatus::Completed,
            'confidence' => DiagnosisConfidence::Medium,
            'urgency' => DiagnosisUrgency::High,
            'description' => 'Battery clicking',
            'symptoms_json' => ['clicking'],
            'ai_response' => ['diagnosis' => 'Battery issue'],
        ]);

        $this->actingAs($this->admin)->get("/admin/diagnoses?status=completed&urgency=high&confidence=medium&affected_category={$category->id}")
            ->assertOk()
            ->assertSee('#' . $diagnosis->id)
            ->assertSee('Electricity');

        $this->actingAs($this->admin)->get("/admin/diagnoses/{$diagnosis->id}")
            ->assertOk()
            ->assertSee('AI Response')
            ->assertSee('Suggested Workshops');

        $this->actingAs($this->admin)->post("/admin/diagnoses/{$diagnosis->id}/manual-review")->assertRedirect();
        $this->assertSame(DiagnosisStatus::ManualReview, $diagnosis->refresh()->status);

        $this->actingAs($this->admin)->post("/admin/diagnoses/{$diagnosis->id}/complete")->assertRedirect();
        $this->assertSame(DiagnosisStatus::Completed, $diagnosis->refresh()->status);
    }

    public function test_admin_can_filter_cancel_and_force_complete_bookings(): void
    {
        $booking = Booking::factory()->create(['status' => BookingStatus::Pending]);

        $this->actingAs($this->admin)->get("/admin/bookings?status=pending&workshop={$booking->workshop_id}&service={$booking->service_id}&date_from=" . now()->toDateString())
            ->assertOk()
            ->assertSee('#' . $booking->id);

        $this->actingAs($this->admin)->get("/admin/bookings/{$booking->id}")
            ->assertOk()
            ->assertSee('Status Logs');

        $this->actingAs($this->admin)->post("/admin/bookings/{$booking->id}/cancel", [
            'admin_notes' => 'Customer requested cancellation.',
        ])->assertRedirect();

        $this->assertSame(BookingStatus::Cancelled, $booking->refresh()->status);
        $this->assertDatabaseHas('booking_status_logs', [
            'booking_id' => $booking->id,
            'old_status' => BookingStatus::Pending->value,
            'new_status' => BookingStatus::Cancelled->value,
            'changed_by' => $this->admin->id,
        ]);

        $this->actingAs($this->admin)->post("/admin/bookings/{$booking->id}/complete", [
            'admin_notes' => 'Force completed by admin.',
        ])->assertRedirect();

        $this->assertSame(BookingStatus::Completed, $booking->refresh()->status);
        $this->assertNotNull($booking->completed_at);
    }
}
