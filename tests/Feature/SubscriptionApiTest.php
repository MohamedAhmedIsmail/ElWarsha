<?php

namespace Tests\Feature;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\RecordStatus;
use App\Enums\SubscriptionStatus;
use App\Enums\UserRole;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubscriptionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_plans_return_active_only(): void
    {
        Plan::factory()->create(['name' => 'Basic', 'code' => 'basic', 'price' => 299]);
        Plan::factory()->inactive()->create(['name' => 'Hidden', 'code' => 'hidden']);

        $this->getJson('/api/plans')
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonPath('data.items.0.name', 'Basic');
    }

    public function test_workshop_owner_can_request_pending_subscription_and_payment(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create(['role' => UserRole::WorkshopOwner]);
        $workshop = Workshop::factory()->approved()->create([
            'owner_id' => $owner->id,
            'subscription_status' => 'free',
        ]);
        $plan = Plan::factory()->create([
            'name' => 'Pro',
            'code' => 'pro',
            'price' => 699,
            'duration_days' => 30,
        ]);

        $response = $this->actingAs($owner, 'sanctum')->postJson('/api/workshop/subscription/request', [
            'plan_id' => $plan->id,
            'payment_method' => PaymentMethod::Instapay->value,
            'transaction_reference' => 'ABC123',
            'receipt_image' => UploadedFile::fake()->image('receipt.jpg'),
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.subscription.status', SubscriptionStatus::Pending->value)
            ->assertJsonPath('data.subscription.plan.id', $plan->id)
            ->assertJsonPath('data.subscription.payments.0.status', PaymentStatus::Pending->value)
            ->assertJsonPath('data.subscription.payments.0.payment_method', PaymentMethod::Instapay->value)
            ->assertJsonPath('data.subscription.payments.0.transaction_reference', 'ABC123');

        $receiptPath = $response->json('data.subscription.payments.0.receipt_image');
        Storage::disk('public')->assertExists($receiptPath);

        $this->assertDatabaseHas('subscriptions', [
            'workshop_id' => $workshop->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Pending->value,
            'auto_renew' => false,
        ]);
        $this->assertDatabaseHas('payments', [
            'user_id' => $owner->id,
            'workshop_id' => $workshop->id,
            'amount' => '699',
            'payment_method' => PaymentMethod::Instapay->value,
            'transaction_reference' => 'ABC123',
            'status' => PaymentStatus::Pending->value,
        ]);
        $this->assertSame('free', $workshop->refresh()->subscription_status);
    }

    public function test_workshop_owner_can_view_latest_subscription(): void
    {
        $owner = User::factory()->create(['role' => UserRole::WorkshopOwner]);
        $workshop = Workshop::factory()->approved()->create(['owner_id' => $owner->id]);
        $old = Subscription::factory()->create(['workshop_id' => $workshop->id]);
        $latest = Subscription::factory()->create(['workshop_id' => $workshop->id]);
        Payment::factory()->create([
            'workshop_id' => $workshop->id,
            'subscription_id' => $latest->id,
            'status' => PaymentStatus::Pending,
        ]);

        $this->actingAs($owner, 'sanctum')->getJson('/api/workshop/subscription')
            ->assertOk()
            ->assertJsonPath('data.subscription.id', $latest->id)
            ->assertJsonPath('data.subscription.payments.0.subscription_id', $latest->id);

        $this->assertNotSame($old->id, $latest->id);
    }

    public function test_subscription_request_requires_workshop_owner_with_profile(): void
    {
        $customer = User::factory()->create();
        $ownerWithoutWorkshop = User::factory()->create(['role' => UserRole::WorkshopOwner]);
        $plan = Plan::factory()->create();

        $payload = [
            'plan_id' => $plan->id,
            'payment_method' => PaymentMethod::Instapay->value,
        ];

        $this->actingAs($customer, 'sanctum')->postJson('/api/workshop/subscription/request', $payload)
            ->assertNotFound();

        $this->actingAs($ownerWithoutWorkshop, 'sanctum')->postJson('/api/workshop/subscription/request', $payload)
            ->assertNotFound();
    }

    public function test_inactive_plan_cannot_be_requested(): void
    {
        $owner = User::factory()->create(['role' => UserRole::WorkshopOwner]);
        Workshop::factory()->approved()->create(['owner_id' => $owner->id]);
        $plan = Plan::factory()->inactive()->create(['status' => RecordStatus::Inactive]);

        $this->actingAs($owner, 'sanctum')->postJson('/api/workshop/subscription/request', [
            'plan_id' => $plan->id,
            'payment_method' => PaymentMethod::BankTransfer->value,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['plan_id']);
    }
}
