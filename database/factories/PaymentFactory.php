<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'workshop_id' => Workshop::factory()->approved(),
            'subscription_id' => Subscription::factory(),
            'amount' => fake()->randomFloat(2, 100, 2000),
            'payment_method' => PaymentMethod::Instapay,
            'transaction_reference' => fake()->bothify('TXN###???'),
            'receipt_image' => null,
            'status' => PaymentStatus::Pending,
            'approved_by' => null,
            'approved_at' => null,
            'admin_notes' => null,
        ];
    }
}
