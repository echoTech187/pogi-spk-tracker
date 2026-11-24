<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KemenkesController extends Controller
{
    /**
     * Merekam aktivitas akses / login ke portal Kemenkes Plataran Sehat
     * secara otomatis ke verifier_performance_logs dan audit_logs.
     */
    public function trackAccess(Request $request): JsonResponse
    {
        $user = Auth::user();
        $now = Carbon::now();
        $portalUrl = config('services.kemenkes.portal_url', 'https://satusehat.kemkes.go.id/sdmk/login');

        $doctorId = $request->input('doctor_id');
        $doctorName = $request->input('doctor_name');

        // 1. Simpan ke verifier_performance_logs untuk metrik SLA / Kinerja Admin
        DB::table('verifier_performance_logs')->insert([
            'id' => (string) Str::uuid(),
            'verifier_user_id' => $user->id,
            'doctor_id' => $doctorId,
            'skp_entry_id' => null,
            'document_id' => null,
            'action_type' => 'KEMENKES_SYNC_SESSION',
            'turnaround_time_seconds' => 0,
            'created_at' => $now,
        ]);

        // 2. Simpan ke jejak audit permanen audit_logs
        DB::table('audit_logs')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'action' => 'KEMENKES_PORTAL_ACCESS',
            'entity_name' => 'kemenkes_portal',
            'entity_id' => $doctorId ?? $user->id,
            'old_values' => null,
            'new_values' => json_encode([
                'event' => 'Akses Portal Kemenkes Plataran Sehat',
                'user_name' => $user->full_name,
                'role' => $user->role,
                'doctor_context' => $doctorName ?? ($doctorId ? "Dokter ID: {$doctorId}" : 'Monitoring Umum Dashboard'),
                'source' => $request->input('source', 'dashboard_button'),
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => $now,
        ]);

        // Jika ada konteks dokter spesifik, perbarui kemenkes_updated_at pada tabel doctors
        if ($doctorId) {
            DB::table('doctors')
                ->where('id', $doctorId)
                ->update([
                    'kemenkes_updated_at' => $now,
                    'updated_at' => $now,
                ]);
        }

        return response()->json([
            'success' => true,
            'status' => 'success',
            'portal_url' => $portalUrl,
            'message' => 'Sesi kerja Kemenkes tercatat otomatis ke jejak kinerja admin.',
            'timestamp' => $now->toDateTimeString(),
            'verifier' => $user->full_name,
        ]);
    }

    /**
     * Redirect langsung ke portal Kemenkes setelah otomatis mencatat log.
     */
    public function launchAndTrack(Request $request): RedirectResponse
    {
        $this->trackAccess($request);
        $portalUrl = config('services.kemenkes.portal_url', 'https://satusehat.kemkes.go.id/sdmk/login');

        return redirect()->away($portalUrl);
    }
}
