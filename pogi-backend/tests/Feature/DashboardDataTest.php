<?php

namespace Tests\Feature;

use App\Application\DTOs\DashboardSummaryDto;
use App\Domain\Contracts\DashboardRepositoryInterface;
use App\Domain\Entities\SystemRole;
use App\Models\User;
use Tests\TestCase;

class DashboardDataTest extends TestCase
{
    public function test_guest_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_super_admin_can_access_dashboard_with_summary_data(): void
    {
        $user = new User([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'role' => SystemRole::SUPER_ADMIN,
            'full_name' => 'Super Admin Test',
            'email' => 'super@example.com',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
        $response->assertViewHas('summary');
        $response->assertViewHas('user');

        $summary = $response->viewData('summary');
        $this->assertInstanceOf(DashboardSummaryDto::class, $summary);
        $this->assertGreaterThan(0, $summary->totalDoctors);
        $this->assertGreaterThan(0, $summary->avgProgressPct);
        $this->assertCount(5, $summary->lowestProvinces);
        $this->assertCount(5, $summary->highestProvinces);
        $this->assertCount(38, $summary->provincesData);

        // Verify HTML contains dynamic numbers
        $response->assertSee('Total Doctors');
        $response->assertSee((string) $summary->totalDoctors);
        $response->assertSee((string) $summary->avgProgressPct . '%');
        $response->assertSee((string) $summary->completeCount);
        $response->assertSee((string) $summary->incompleteCount);
    }

    public function test_admin_cabang_can_access_dashboard_with_branch_scoping(): void
    {
        $jabarBranch = \App\Infrastructure\Persistence\Eloquent\PogiBranch::where('branch_code', 'POGI-32')->first();

        $user = new User([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'role' => SystemRole::ADMIN_CABANG,
            'full_name' => 'Admin Cabang Jabar',
            'email' => 'cabang.jabar@example.com',
            'branch_id' => $jabarBranch ? $jabarBranch->id : null,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $summary = $response->viewData('summary');
        $this->assertInstanceOf(DashboardSummaryDto::class, $summary);
        $this->assertEquals('POGI Cabang Jawa Barat', $summary->activeScopeLabel);
    }

    public function test_query_scope_filter_works_correctly(): void
    {
        $user = new User([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'role' => SystemRole::SUPER_ADMIN,
            'full_name' => 'Super Admin Test',
            'email' => 'super@example.com',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/dashboard?scope=dki');

        $response->assertStatus(200);
        $summary = $response->viewData('summary');
        $this->assertInstanceOf(DashboardSummaryDto::class, $summary);
        $this->assertEquals('POGI Cabang DKI Jakarta', $summary->activeScopeLabel);
        $this->assertGreaterThanOrEqual(20, $summary->totalDoctors);
    }

    public function test_repository_interface_is_properly_bound_in_container(): void
    {
        $repo = app(DashboardRepositoryInterface::class);
        $this->assertInstanceOf(DashboardRepositoryInterface::class, $repo);

        $summary = $repo->getDashboardSummary();
        $this->assertInstanceOf(DashboardSummaryDto::class, $summary);
    }

    public function test_super_admin_can_send_follow_up_to_database_queue(): void
    {
        $user = new User([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'role' => SystemRole::SUPER_ADMIN,
            'full_name' => 'Super Admin Test',
            'email' => 'super@example.com',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->postJson('/dashboard/send-follow-up', [
            'target' => 'lowest_5',
            'channels' => ['whatsapp'],
            'message' => 'Pengingat kelengkapan 160 SKP resmi POGI.'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $this->assertDatabaseHas('wa_blast_campaigns', [
            'template_type' => 'FOLLOW_UP_REMINDER'
        ]);
    }
}
