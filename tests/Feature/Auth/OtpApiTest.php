<?php

namespace Tests\Feature\Auth;

use App\Enums\OtpPurpose;
use App\Models\OtpCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OtpApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_otp(): void
    {
        $response = $this->postJson('/api/auth/request-otp', [
            'phone' => '01000000003',
            'purpose' => OtpPurpose::Register->value,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['expires_at', 'otp']]);

        $this->assertDatabaseHas('otp_codes', [
            'phone' => '01000000003',
            'purpose' => OtpPurpose::Register->value,
            'used_at' => null,
        ]);
    }

    public function test_otp_can_only_be_used_once(): void
    {
        $otpCode = OtpCode::query()->create([
            'phone' => '01000000004',
            'code' => '123456',
            'purpose' => OtpPurpose::Login,
            'expires_at' => now()->addMinutes(5),
        ]);

        $payload = [
            'phone' => $otpCode->phone,
            'code' => $otpCode->code,
            'purpose' => OtpPurpose::Login->value,
        ];

        $this->postJson('/api/auth/verify-otp', $payload)
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->postJson('/api/auth/verify-otp', $payload)
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['code']);
    }

    public function test_expired_otp_cannot_be_verified(): void
    {
        OtpCode::query()->create([
            'phone' => '01000000005',
            'code' => '123456',
            'purpose' => OtpPurpose::ResetPassword,
            'expires_at' => now()->subMinute(),
        ]);

        $this->postJson('/api/auth/verify-otp', [
            'phone' => '01000000005',
            'code' => '123456',
            'purpose' => OtpPurpose::ResetPassword->value,
        ])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['code']);
    }
}
