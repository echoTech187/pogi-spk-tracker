<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DoctorLiveSyncTest extends TestCase
{
    protected function tearDown(): void
    {
        DB::table('doctors')->where('full_name', 'like', '%Test Live Sync%')->delete();
        DB::table('skp_entries')->where('title', 'like', '%Live Test%')->delete();
        DB::table('doctor_documents')->where('file_name', 'like', '%Live_Test%')->delete();
        DB::table('doctor_documents')->where('file_name', 'like', '%Dokumen_Status_Test%')->delete();
        parent::tearDown();
    }

    protected function getSuperAdmin(): User
    {
        return User::where('role', 'SUPER_ADMIN')->first()
            ?? User::factory()->create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'role' => 'SUPER_ADMIN',
                'is_active' => true,
            ]);
    }

    protected function getAdminPogi(): User
    {
        return User::where('role', 'ADMIN_POGI')->first()
            ?? User::factory()->create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'role' => 'ADMIN_POGI',
                'is_active' => true,
            ]);
    }

    protected function getAdminCabang(): User
    {
        return User::where('role', 'ADMIN_CABANG')->first()
            ?? User::factory()->create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'role' => 'ADMIN_CABANG',
                'is_active' => true,
            ]);
    }

    public function test_doctors_page_renders_with_live_data_and_status(): void
    {
        $admin = $this->getSuperAdmin();

        $response = $this->actingAs($admin)->get('/doctors');

        $response->assertStatus(200);
        $response->assertViewHas('doctors');
        $response->assertSee('LIVE_DATABASE_DOCTORS', false);
    }

    public function test_doctor_detail_renders_with_live_documents_and_detector(): void
    {
        $admin = $this->getSuperAdmin();
        $doctor = DB::table('doctors')->first();
        $this->assertNotNull($doctor, 'Database should have at least one doctor');

        $response = $this->actingAs($admin)->get('/doctor-detail?id=' . $doctor->id);

        $response->assertStatus(200);
        $response->assertViewHas('doctor');
        $response->assertViewHas('documents');
        $response->assertViewHas('skpEntries');
        $response->assertViewHas('missingDocs');
        $response->assertSee('LIVE_SERVER_DOCTOR', false);
        $response->assertSee('LIVE_SERVER_MISSING_DOCS', false);
    }

    public function test_super_admin_can_store_new_doctor_in_database(): void
    {
        $admin = $this->getSuperAdmin();

        $name = 'dr. Test Live Sync, Sp.OG';
        $str = '32.73.999.2.26.' . rand(1000, 9999);

        $response = $this->actingAs($admin)->postJson('/doctors', [
            'name' => $name,
            'str' => $str,
            'phone' => '+6281299999999',
            'province' => 'DKI Jakarta',
            'institution' => 'RSUP Dr. Cipto Mangunkusumo',
            'assigned_to' => 'Siti Rahma',
            'period_start' => '1 Jan 2026',
            'period_end' => '31 Des 2030',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'success',
        ]);

        $doctorId = $response->json('doctor.id');
        $this->assertNotNull($doctorId);

        // Verify inserted in DB
        $dbDoctor = DB::table('doctors')->where('id', $doctorId)->first();
        $this->assertNotNull($dbDoctor);
        $this->assertEquals($name, $dbDoctor->full_name);

        // Verify SKP period created
        $dbPeriod = DB::table('doctor_skp_periods')->where('doctor_id', $doctorId)->first();
        $this->assertNotNull($dbPeriod);
        $this->assertEquals(160.0, (float) $dbPeriod->target_total_credits);
    }

    public function test_verify_credits_updates_pending_entries_and_period(): void
    {
        $admin = $this->getSuperAdmin();
        $doctor = DB::table('doctors')->first();
        $this->assertNotNull($doctor);

        $period = DB::table('doctor_skp_periods')->where('doctor_id', $doctor->id)->first();
        $category = DB::table('event_categories')->first();

        // Insert dummy pending skp entry
        $entryId = (string) \Illuminate\Support\Str::uuid();
        DB::table('skp_entries')->insert([
            'id' => $entryId,
            'doctor_id' => $doctor->id,
            'period_id' => $period ? $period->id : (string) \Illuminate\Support\Str::uuid(),
            'category_id' => $category ? $category->id : (string) \Illuminate\Support\Str::uuid(),
            'title' => 'Simposium Obgyn Live Test',
            'domain' => 'PEMBELAJARAN',
            'activity_date' => now()->toDateString(),
            'credit_amount' => 5.0,
            'status' => 'SUBMITTED',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($admin)->postJson("/doctors/{$doctor->id}/verify-credits");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'success',
        ]);

        // Entry should now be DONE
        $updatedEntry = DB::table('skp_entries')->where('id', $entryId)->first();
        $this->assertEquals('DONE', $updatedEntry->status);
    }

    public function test_upload_document_creates_database_record(): void
    {
        $admin = $this->getAdminPogi();
        $doctor = DB::table('doctors')->first();
        $this->assertNotNull($doctor);

        $filename = 'Sertifikat_Pelayanan_Live_Test.pdf';
        $response = $this->actingAs($admin)->postJson("/doctors/{$doctor->id}/documents", [
            'filename' => $filename,
            'category' => 'Pelayanan',
            'skp_value' => 3.5,
            'google_drive_url' => 'https://drive.google.com/file/d/test-doc/view',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'success',
        ]);

        $docId = $response->json('document.id');
        $this->assertNotNull($docId);

        $dbDoc = DB::table('doctor_documents')->where('id', $docId)->first();
        $this->assertNotNull($dbDoc);
        $this->assertEquals($filename, $dbDoc->file_name);
        $this->assertEquals('https://drive.google.com/file/d/test-doc/view', $dbDoc->google_drive_url);
    }

    public function test_update_document_status_persists_in_database(): void
    {
        $admin = $this->getSuperAdmin();
        $doctor = DB::table('doctors')->first();
        $this->assertNotNull($doctor);

        $docId = (string) \Illuminate\Support\Str::uuid();
        DB::table('doctor_documents')->insert([
            'id' => $docId,
            'doctor_id' => $doctor->id,
            'skp_entry_id' => null,
            'document_type' => 'SERTIFIKAT_PEMBELAJARAN',
            'file_name' => 'Dokumen_Status_Test.pdf',
            'google_drive_url' => 'https://drive.google.com/test-doc',
            'is_verified' => false,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($admin)->postJson("/doctors/{$doctor->id}/documents/{$docId}/status", [
            'status' => 'DONE',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $updatedDoc = DB::table('doctor_documents')->where('id', $docId)->first();
        $this->assertTrue((bool) $updatedDoc->is_verified);
    }

    public function test_admin_cabang_cannot_verify_doctor_credits_or_modify_documents(): void
    {
        $adminCabang = $this->getAdminCabang();
        $doctor = DB::table('doctors')->first();
        $this->assertNotNull($doctor);

        $response = $this->actingAs($adminCabang)->postJson("/doctors/{$doctor->id}/verify-credits");
        $response->assertStatus(403);

        $response2 = $this->actingAs($adminCabang)->postJson("/doctors/{$doctor->id}/documents", [
            'filename' => 'Unauthorized.pdf',
            'category' => 'Pembelajaran',
        ]);
        $response2->assertStatus(403);
    }
}
