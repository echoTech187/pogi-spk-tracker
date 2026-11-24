<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DoctorController extends Controller
{
    /**
     * Tampilkan halaman direktori dokter.
     */
    public function index(Request $request)
    {
        return view('doctors');
    }

    /**
     * Tampilkan detail portofolio dokter.
     */
    public function show(Request $request)
    {
        return view('doctor-detail');
    }

    /**
     * Alihkan / Tentukan Admin PIC pengelola dokter.
     * HANYA DAPAT DIAKSES OLEH SUPER_ADMIN (Sesuai Aturan Klien & RULES.md).
     */
    public function assignPic(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();

        // Otorisasi ketat: Hanya Super Admin yang berhak mengubah PIC dokter
        if (strtoupper($user->role) !== 'SUPER_ADMIN') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: Hanya Super Admin yang berwenang menentukan atau mengubah Admin PIC pengelola dokter.',
            ], 403);
        }

        $request->validate([
            'assigned_pic_id' => 'required|uuid|exists:users,id',
        ]);

        $doctor = DB::table('doctors')->where('id', $id)->first();
        if (!$doctor) {
            return response()->json([
                'success' => false,
                'message' => 'Data dokter tidak ditemukan.',
            ], 404);
        }

        $targetPic = DB::table('users')->where('id', $request->input('assigned_pic_id'))->first();
        if (!$targetPic) {
            return response()->json([
                'success' => false,
                'message' => 'Admin PIC tujuan tidak ditemukan.',
            ], 404);
        }

        $oldPicId = $doctor->assigned_pic_id;
        $now = Carbon::now();

        // Update database
        DB::table('doctors')->where('id', $id)->update([
            'assigned_pic_id' => $targetPic->id,
            'updated_at' => $now,
        ]);

        // Rekam ke jejak audit audit_logs
        DB::table('audit_logs')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'action' => 'REASSIGN_DOCTOR_PIC',
            'entity_name' => 'doctors',
            'entity_id' => $id,
            'old_values' => json_encode(['assigned_pic_id' => $oldPicId]),
            'new_values' => json_encode([
                'doctor_name' => $doctor->full_name,
                'new_pic_id' => $targetPic->id,
                'new_pic_name' => $targetPic->full_name,
                'new_pic_role' => $targetPic->role,
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => $now,
        ]);

        return response()->json([
            'success' => true,
            'status' => 'success',
            'message' => "PIC Dokter {$doctor->full_name} berhasil dialihkan ke {$targetPic->full_name}.",
            'pic' => [
                'id' => $targetPic->id,
                'name' => $targetPic->full_name,
                'role' => $targetPic->role,
            ],
        ]);
    }
}
