<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthAndRbacMiddlewareTest extends TestCase
{
    /**
     * Helper to create a user with a given role
     */
    private function makeUser(string $role, ?string $branchId = null, bool $isActive = true): User
    {
        $user = new User();
        $user->id = (string) Str::uuid();
        $user->role = $role;
        $user->full_name = "User {$role}";
        $user->email = strtolower($role) . "@pogi.or.id";
        $user->branch_id = $branchId;
        $user->is_active = $isActive;
        return $user;
    }

    public function test_guest_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('POGI SKP TRACKER');
    }

    public function test_super_admin_can_access_dashboard_and_manage_module(): void
    {
        $superAdmin = $this->makeUser('SUPER_ADMIN');

        $this->actingAs($superAdmin)
            ->get('/dashboard')
            ->assertStatus(200);

        $this->actingAs($superAdmin)
            ->get('/manage')
            ->assertStatus(200);
    }

    public function test_admin_cabang_is_forbidden_from_manage_module(): void
    {
        $adminCabang = $this->makeUser('ADMIN_CABANG', '01935d01-8140-7002-8000-000000000002');

        // Can access dashboard
        $this->actingAs($adminCabang)
            ->get('/dashboard')
            ->assertStatus(200);

        // Forbidden from manage module (403)
        $this->actingAs($adminCabang)
            ->get('/manage')
            ->assertStatus(403);
    }

    public function test_admin_pogi_is_forbidden_from_admin_performance_module(): void
    {
        $adminPogi = $this->makeUser('ADMIN_POGI');

        // Can access dashboard
        $this->actingAs($adminPogi)
            ->get('/dashboard')
            ->assertStatus(200);

        // Forbidden from admin performance (Khusus Super Admin & Pusat)
        $this->actingAs($adminPogi)
            ->get('/admin-performance')
            ->assertStatus(403);
    }

    public function test_doctor_can_access_doctor_detail_portfolio(): void
    {
        $doctor = $this->makeUser('USER_DOKTER');

        $this->actingAs($doctor)
            ->get('/doctor-detail')
            ->assertStatus(200);
    }

    public function test_inactive_user_is_blocked_by_active_middleware(): void
    {
        $inactiveUser = $this->makeUser('SUPER_ADMIN', null, false);

        $this->actingAs($inactiveUser)
            ->get('/dashboard')
            ->assertRedirect('/login');
    }

    public function test_user_can_login_via_post_and_redirects_to_dashboard(): void
    {
        $response = $this->post('/login', [
            'email'    => 'super@example.com',
            'password' => 'Super2026!Master',
            'remember' => 'on',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_doctor_login_redirects_to_doctor_portfolio(): void
    {
        $response = $this->post('/login', [
            'email'    => 'cut.simatupang@pogi.or.id',
            'password' => 'DokterCut2026!',
        ]);

        $response->assertRedirect(route('doctors.show', ['id' => 'doc-001']));
        $this->assertAuthenticated();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $superAdmin = $this->makeUser('SUPER_ADMIN');

        $this->actingAs($superAdmin)
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout_via_get(): void
    {
        $superAdmin = $this->makeUser('SUPER_ADMIN');

        $this->actingAs($superAdmin)
            ->get('/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
    }
}
