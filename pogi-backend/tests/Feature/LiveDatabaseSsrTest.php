<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LiveDatabaseSsrTest extends TestCase
{
    private function getSuperAdmin(): User
    {
        return User::where('role', 'SUPER_ADMIN')->firstOrFail();
    }

    public function test_reports_page_renders_live_database_data(): void
    {
        $superAdmin = $this->getSuperAdmin();

        $response = $this->actingAs($superAdmin)->get('/reports');

        $response->assertStatus(200)
            ->assertViewHas('snapshots')
            ->assertViewHas('monthlyData')
            ->assertViewHas('metrics');

        $this->assertNotEmpty($response->viewData('monthlyData'));
    }

    public function test_provinces_page_renders_38_provinces_live(): void
    {
        $superAdmin = $this->getSuperAdmin();

        $response = $this->actingAs($superAdmin)->get('/provinces');

        $response->assertStatus(200)
            ->assertViewHas('provinces')
            ->assertViewHas('nationalSummary');

        $provinces = $response->viewData('provinces');
        $this->assertCount(38, $provinces);
    }

    public function test_events_page_renders_live_events_and_categories(): void
    {
        $superAdmin = $this->getSuperAdmin();

        $response = $this->actingAs($superAdmin)->get('/events');

        $response->assertStatus(200)
            ->assertViewHas('events')
            ->assertViewHas('categories')
            ->assertViewHas('metrics');
    }

    public function test_activity_page_renders_live_audit_logs(): void
    {
        $superAdmin = $this->getSuperAdmin();

        $response = $this->actingAs($superAdmin)->get('/activity');

        $response->assertStatus(200)
            ->assertViewHas('logs')
            ->assertViewHas('metrics');
    }

    public function test_admin_performance_renders_live_team_metrics(): void
    {
        $superAdmin = $this->getSuperAdmin();

        $response = $this->actingAs($superAdmin)->get('/admin-performance');

        $response->assertStatus(200)
            ->assertViewHas('admins')
            ->assertViewHas('summary');
    }

    public function test_manage_page_renders_all_master_entities(): void
    {
        $superAdmin = $this->getSuperAdmin();

        $response = $this->actingAs($superAdmin)->get('/manage');

        $response->assertStatus(200)
            ->assertViewHas('users')
            ->assertViewHas('branches')
            ->assertViewHas('doctors')
            ->assertViewHas('provinces')
            ->assertViewHas('institutions')
            ->assertViewHas('categories')
            ->assertViewHas('events');
    }

    public function test_doctors_page_renders_live_directory(): void
    {
        $superAdmin = $this->getSuperAdmin();

        $response = $this->actingAs($superAdmin)->get('/doctors');

        $response->assertStatus(200)
            ->assertViewHas('doctors')
            ->assertViewHas('metrics')
            ->assertViewHas('provinces')
            ->assertViewHas('institutions');
    }
}
