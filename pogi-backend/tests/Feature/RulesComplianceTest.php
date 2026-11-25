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
}
