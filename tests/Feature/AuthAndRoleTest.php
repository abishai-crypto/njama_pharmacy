<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class AuthAndRoleTest extends TestCase
{
    public function test_user_can_login_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'login' => 'dr.njama',
            'password' => 'Admin@12345',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/');
        $this->assertAuthenticated();
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        $response = $this->post('/login', [
            'login' => 'dr.njama',
            'password' => 'WrongPassword123',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $admin = User::where('username', 'dr.njama')->first();
        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('NJAMA PHARMACY');
        $response->assertSee('Dr. Njama');
    }

    public function test_cashier_cannot_access_admin_settings(): void
    {
        $cashier = User::where('username', 'cashier')->first();
        $response = $this->actingAs($cashier)->get('/settings');

        // Should be forbidden (403)
        $response->assertStatus(403);
    }

    public function test_admin_can_access_settings(): void
    {
        $admin = User::where('username', 'dr.njama')->first();
        $response = $this->actingAs($admin)->get('/settings');

        $response->assertStatus(200);
        $response->assertSee('0715362428');
        $response->assertSee('0656392400');
    }

    public function test_language_switch_sets_session(): void
    {
        $response = $this->get('/lang/sw');
        $response->assertSessionHas('locale', 'sw');

        $responseEn = $this->get('/lang/en');
        $responseEn->assertSessionHas('locale', 'en');
    }
}
