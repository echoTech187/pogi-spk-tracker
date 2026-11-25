<?php

namespace Tests\Feature;

use App\Infrastructure\Security\NikCipher;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RulesComplianceTest extends TestCase
{
    public function test_nik_cipher_encrypts_with_aes_256_gcm_and_hashes_deterministically(): void
    {
        $nik = '3171012345670001';
        $encrypted = NikCipher::encrypt($nik);

        $this->assertStringStartsWith('GCM1:', $encrypted);
        $this->assertNotEquals($nik, $encrypted);
        $this->assertEquals($nik, NikCipher::decrypt($encrypted));

        $hash1 = NikCipher::hash($nik);
        $hash2 = NikCipher::hash('  ' . $nik . ' ');
        $this->assertEquals(64, strlen($hash1));
        $this->assertEquals($hash1, $hash2);
    }

    public function test_upload_document_requires_mandatory_google_drive_url(): void
    {
        $admin = User::where('role', 'ADMIN_POGI')->first();
        $doctor = DB::table('doctors')->first();

        $response = $this->actingAs($admin)->postJson("/doctors/{$doctor->id}/documents", [
            'filename' => 'Sertifikat_Tanpa_Drive.pdf',
            'category' => 'Pembelajaran',
            'skp_value' => 2.0,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['google_drive_url']);
    }

    public function test_artisan_scheduler_commands_are_registered(): void
    {
        $this->artisan('list pogi')
            ->expectsOutputToContain('pogi:monthly-cutoff')
            ->expectsOutputToContain('pogi:wa-reminder')
            ->expectsOutputToContain('pogi:wa-process-queue')
            ->assertExitCode(0);
    }

    public function test_admin_pogi_only_sees_assigned_pic_doctors(): void
    {
        $anna = User::where('role', 'ADMIN_POGI')->first();
        $expectedCount = DB::table('doctors')
            ->whereNull('deleted_at')
            ->where('assigned_pic_id', $anna->id)
            ->count();

        $dashboard = $this->actingAs($anna)->get('/dashboard');
        $dashboard->assertStatus(200);
        $summary = $dashboard->viewData('summary');
        $this->assertEquals($expectedCount, $summary->totalDoctors);

        $doctorsPage = $this->actingAs($anna)->get('/doctors');
        $doctorsPage->assertStatus(200);
        $this->assertCount($expectedCount, $doctorsPage->viewData('doctors'));

        $provincesPage = $this->actingAs($anna)->get('/provinces');
        $provincesPage->assertStatus(200);
        foreach ($provincesPage->viewData('provinces') as $prov) {
            $this->assertGreaterThan(0, $prov['doctors']);
        }

        $otherDoctor = DB::table('doctors')
            ->whereNull('deleted_at')
            ->where('assigned_pic_id', '!=', $anna->id)
            ->first();
        if ($otherDoctor) {
            $this->actingAs($anna)->get("/doctor-detail?id={$otherDoctor->id}")->assertStatus(403);
        }
    }
}
