<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private function setupRole(string $name): Role
    {
        return Role::create(['name' => $name, 'description' => $name]);
    }

    public function test_login_page_is_accessible(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Masuk');
    }

    public function test_user_can_login_with_email(): void
    {
        $role = $this->setupRole(User::ROLE_SUPER_ADMIN);
        User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => 'password123',
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $this->post(route('login'), [
            'login' => 'admin@test.com',
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_user_can_login_with_nisn(): void
    {
        $role = $this->setupRole(User::ROLE_SISWA);
        User::create([
            'name' => 'Test Siswa',
            'email' => 'siswa@test.com',
            'password' => 'password123',
            'role_id' => $role->id,
            'nisn' => '00123456',
            'is_active' => true,
        ]);

        $this->post(route('login'), [
            'login' => '00123456',
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $this->post(route('login'), [
            'login' => 'unknown@test.com',
            'password' => 'wrongpassword',
        ])->assertRedirect();

        $this->assertGuest();
    }

    public function test_login_rejects_inactive_account(): void
    {
        $role = $this->setupRole(User::ROLE_ADMIN);
        User::create([
            'name' => 'Inactive User',
            'email' => 'inactive@test.com',
            'password' => 'password123',
            'role_id' => $role->id,
            'is_active' => false,
        ]);

        $this->post(route('login'), [
            'login' => 'inactive@test.com',
            'password' => 'password123',
        ])->assertRedirect();

        $this->assertGuest();
    }

    public function test_logout_redirects_to_login(): void
    {
        $role = $this->setupRole(User::ROLE_ADMIN);
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => 'password123',
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $this->actingAs($user)->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }
}
