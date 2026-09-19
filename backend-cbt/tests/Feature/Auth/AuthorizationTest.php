<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $roleName): User
    {
        $role = Role::create(['name' => $roleName, 'description' => $roleName]);

        return User::create([
            'name' => ucfirst($roleName),
            'email' => str_replace('_', '.', $roleName).'@test.com',
            'password' => 'password',
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    public function test_super_admin_can_access_user_management(): void
    {
        $user = $this->makeUser(User::ROLE_SUPER_ADMIN);
        $this->actingAs($user)->get(route('users.index'))->assertOk();
    }

    public function test_admin_can_access_user_management(): void
    {
        $user = $this->makeUser(User::ROLE_ADMIN);
        $this->actingAs($user)->get(route('users.index'))->assertOk();
    }

    public function test_guru_cannot_access_user_management(): void
    {
        $user = $this->makeUser(User::ROLE_GURU);
        $this->actingAs($user)->get(route('users.index'))->assertRedirect(route('dashboard'));
    }

    public function test_siswa_cannot_access_user_management(): void
    {
        $user = $this->makeUser(User::ROLE_SISWA);
        $this->actingAs($user)->get(route('users.index'))->assertRedirect(route('dashboard'));
    }

    public function test_guru_can_access_questions(): void
    {
        $user = $this->makeUser(User::ROLE_GURU);
        $this->actingAs($user)->get(route('questions.index'))->assertOk();
    }

    public function test_siswa_cannot_access_questions(): void
    {
        $user = $this->makeUser(User::ROLE_SISWA);
        $this->actingAs($user)->get(route('questions.index'))->assertRedirect(route('dashboard'));
    }

    public function test_guru_can_access_exams(): void
    {
        $user = $this->makeUser(User::ROLE_GURU);
        $this->actingAs($user)->get(route('exams.index'))->assertOk();
    }

    public function test_admin_can_access_sessions(): void
    {
        $user = $this->makeUser(User::ROLE_ADMIN);
        $this->actingAs($user)->get(route('sessions.index'))->assertOk();
    }

    public function test_guru_cannot_access_sessions(): void
    {
        $user = $this->makeUser(User::ROLE_GURU);
        $this->actingAs($user)->get(route('sessions.index'))->assertRedirect(route('dashboard'));
    }

    public function test_proktor_can_access_monitoring(): void
    {
        $user = $this->makeUser(User::ROLE_PROKTOR);
        $this->actingAs($user)->get(route('monitoring.index'))->assertOk();
    }

    public function test_admin_can_access_monitoring(): void
    {
        $user = $this->makeUser(User::ROLE_ADMIN);
        $this->actingAs($user)->get(route('monitoring.index'))->assertOk();
    }

    public function test_guru_cannot_access_monitoring(): void
    {
        $user = $this->makeUser(User::ROLE_GURU);
        $this->actingAs($user)->get(route('monitoring.index'))->assertRedirect(route('dashboard'));
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $this->get(route('questions.index'))->assertRedirect(route('login'));
    }

    public function test_kepala_sekolah_can_access_results(): void
    {
        $user = $this->makeUser(User::ROLE_KEPALA_SEKOLAH);
        $this->actingAs($user)->get(route('results.index'))->assertOk();
    }

    public function test_siswa_cannot_access_results(): void
    {
        $user = $this->makeUser(User::ROLE_SISWA);
        $this->actingAs($user)->get(route('results.index'))->assertRedirect(route('dashboard'));
    }

    public function test_siswa_can_access_dashboard(): void
    {
        $user = $this->makeUser(User::ROLE_SISWA);
        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Siap Melaksanakan Ujian?')
            ->assertSee('Ujian Tersedia')
            ->assertSee('Riwayat Ujian Saya');
    }
}
