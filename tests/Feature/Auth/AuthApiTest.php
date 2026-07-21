<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_public_role_and_receive_token(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Mohamed',
            'phone' => '01000000000',
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => UserRole::Customer->value,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.phone', '01000000000')
            ->assertJsonPath('data.user.role', UserRole::Customer->value)
            ->assertJsonStructure(['data' => ['token', 'token_type']]);

        $this->assertDatabaseHas('users', [
            'phone' => '01000000000',
            'email' => 'test@example.com',
            'role' => UserRole::Customer->value,
        ]);
    }

    public function test_public_registration_cannot_create_admin_users(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Admin',
            'phone' => '01000000001',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => UserRole::Admin->value,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['role']);
    }

    public function test_user_can_login_and_receive_token(): void
    {
        User::factory()->create([
            'phone' => '01000000002',
            'password' => Hash::make('password'),
            'status' => UserStatus::Active,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'phone' => '01000000002',
            'password' => 'password',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['token', 'token_type', 'user']]);
    }

    public function test_authenticated_user_can_view_and_update_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('data.user.id', $user->id);

        $this->actingAs($user, 'sanctum')
            ->putJson('/api/me', [
                'name' => 'Updated Name',
                'city' => 'Cairo',
                'area' => 'Nasr City',
            ])
            ->assertOk()
            ->assertJsonPath('data.user.name', 'Updated Name')
            ->assertJsonPath('data.user.city', 'Cairo');
    }
}
