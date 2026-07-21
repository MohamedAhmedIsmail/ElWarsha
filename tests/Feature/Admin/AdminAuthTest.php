<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get('/admin/dashboard')
            ->assertRedirect('/admin/login');
    }

    public function test_admin_can_login_and_view_dashboard(): void
    {
        User::factory()->create([
            'phone' => '01000000000',
            'password' => 'password',
            'role' => UserRole::Admin,
        ]);

        $this->post('/admin/login', [
            'phone' => '01000000000',
            'password' => 'password',
        ])
            ->assertRedirect('/admin/dashboard');

        $this->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Total Users');
    }

    public function test_super_admin_can_access_dashboard(): void
    {
        $superAdmin = User::factory()->create(['role' => UserRole::SuperAdmin]);

        $this->actingAs($superAdmin)->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Super Admin')
            ->assertSee('Monthly Revenue');
    }

    public function test_non_admin_user_gets_403_page(): void
    {
        $customer = User::factory()->create(['role' => UserRole::Customer]);

        $this->actingAs($customer)->get('/admin/dashboard')
            ->assertForbidden()
            ->assertSee('Access denied');
    }

    public function test_non_admin_cannot_login_to_admin_panel(): void
    {
        User::factory()->create([
            'phone' => '01000000001',
            'password' => 'password',
            'role' => UserRole::Customer,
        ]);

        $this->post('/admin/login', [
            'phone' => '01000000001',
            'password' => 'password',
        ])
            ->assertForbidden()
            ->assertSee('Access denied');
    }

    public function test_admin_can_logout(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $this->actingAs($admin)->post('/admin/logout')
            ->assertRedirect('/admin/login');

        $this->assertGuest();
    }
}
