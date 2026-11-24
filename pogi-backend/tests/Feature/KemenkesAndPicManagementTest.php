<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class KemenkesAndPicManagementTest extends TestCase
{
    private function makeUser(string $role, ?string $branchId = null): User
    {
        $user = new User();
        $user->id = (string) Str::uuid();
        $user->role = $role;
        $user->full_name = "User {$role}";
        $user->email = strtolower($role) . '_' . Str::random(5) . "@pogi.or.id";
        $user->branch_id = $branchId;
        $user->is_active = true;
        return $user;
    }

    public function test_kemenkes_track_access_records_verifier_and_audit_logs(): void
    {
        $adminPogi = User::where('role', 'ADMIN_POGI')->first();
        if (!$adminPogi) {
            $adminPogi = $this->makeUser('ADMIN_POGI');
            $adminPogi->save();
        }

        $doctor = DB::table('doctors')->first();
        $doctorId = $doctor ? $doctor->id : null;
        $doctorName = $doctor ? $doctor->full_name : null;

        $response = $this->actingAs($adminPogi)
            ->postJson('/kemenkes/track-access', [
                'doctor_id' => $doctorId,
                'doctor_name' => $doctorName,
                'source' => 'test_suite',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'success',
                'portal_url' => 'https://satusehat.kemkes.go.id',
            ]);

        // Verifikasi log tercatat di database
        $this->assertDatabaseHas('verifier_performance_logs', [
            'verifier_user_id' => $adminPogi->id,
            'action_type' => 'KEMENKES_SYNC_SESSION',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $adminPogi->id,
            'action' => 'KEMENKES_PORTAL_ACCESS',
        ]);
    }

    public function test_kemenkes_launch_redirects_to_satusehat_portal(): void
    {
        $superAdmin = User::where('role', 'SUPER_ADMIN')->first();
        if (!$superAdmin) {
            $superAdmin = $this->makeUser('SUPER_ADMIN');
            $superAdmin->save();
        }

        $response = $this->actingAs($superAdmin)
            ->get('/kemenkes/launch');

        $response->assertRedirect('https://satusehat.kemkes.go.id');
    }

    public function test_guest_cannot_access_kemenkes_tracker(): void
    {
        $this->postJson('/kemenkes/track-access', [])
            ->assertStatus(401);

        $this->get('/kemenkes/launch')
            ->assertRedirect('/login');
    }

    public function test_super_admin_can_assign_doctor_pic(): void
    {
        $superAdmin = User::where('role', 'SUPER_ADMIN')->first();
        if (!$superAdmin) {
            $superAdmin = $this->makeUser('SUPER_ADMIN');
            $superAdmin->save();
        }

        $targetPic = User::where('role', 'ADMIN_POGI')->first();
        if (!$targetPic) {
            $targetPic = $this->makeUser('ADMIN_POGI');
            $targetPic->save();
        }

        $doctor = DB::table('doctors')->first();
        $this->assertNotNull($doctor, 'Tabel doctors harus memiliki data terisi.');

        $response = $this->actingAs($superAdmin)
            ->postJson("/doctors/{$doctor->id}/assign-pic", [
                'assigned_pic_id' => $targetPic->id,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'success',
            ]);

        // Verifikasi perubahan di tabel doctors
        $this->assertDatabaseHas('doctors', [
            'id' => $doctor->id,
            'assigned_pic_id' => $targetPic->id,
        ]);

        // Verifikasi audit log tercatat
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $superAdmin->id,
            'action' => 'REASSIGN_DOCTOR_PIC',
            'entity_name' => 'doctors',
            'entity_id' => $doctor->id,
        ]);
    }

    public function test_admin_pogi_cannot_assign_doctor_pic(): void
    {
        $adminPogi = User::where('role', 'ADMIN_POGI')->first();
        if (!$adminPogi) {
            $adminPogi = $this->makeUser('ADMIN_POGI');
            $adminPogi->save();
        }

        $doctor = DB::table('doctors')->first();
        $this->assertNotNull($doctor);

        // Harus ditolak 403 Forbidden oleh middleware / controller
        $response = $this->actingAs($adminPogi)
            ->postJson("/doctors/{$doctor->id}/assign-pic", [
                'assigned_pic_id' => $adminPogi->id,
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_pusat_and_admin_cabang_cannot_assign_doctor_pic(): void
    {
        $adminPusat = User::where('role', 'ADMIN_PUSAT')->first();
        if (!$adminPusat) {
            $adminPusat = $this->makeUser('ADMIN_PUSAT');
            $adminPusat->save();
        }

        $adminCabang = User::where('role', 'ADMIN_CABANG')->first();
        if (!$adminCabang) {
            $adminCabang = $this->makeUser('ADMIN_CABANG', (string) Str::uuid());
            $adminCabang->save();
        }

        $doctor = DB::table('doctors')->first();
        $this->assertNotNull($doctor);

        // Admin Pusat ditolak 403
        $this->actingAs($adminPusat)
            ->postJson("/doctors/{$doctor->id}/assign-pic", [
                'assigned_pic_id' => $adminPusat->id,
            ])
            ->assertStatus(403);

        // Admin Cabang ditolak 403
        $this->actingAs($adminCabang)
            ->postJson("/doctors/{$doctor->id}/assign-pic", [
                'assigned_pic_id' => $adminCabang->id,
            ])
            ->assertStatus(403);
    }
}
