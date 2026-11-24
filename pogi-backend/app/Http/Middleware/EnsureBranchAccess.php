<?php

namespace App\Http\Middleware;

use App\Infrastructure\Persistence\Eloquent\Doctor;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBranchAccess
{
    /**
     * Membatasi Admin Cabang agar HANYA dapat mengakses dokter di cabangnya sendiri (Isolasi Multi-Tenant)
     * dan Dokter agar HANYA dapat mengakses portofolionya sendiri.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->guest(route('login'));
        }

        // Peran Tingkat Nasional (Super Admin, Admin Pusat, Admin POGI) tidak dibatasi isolasi cabang
        if ($user->isSuperAdmin() || $user->isAdminPusat() || $user->isAdminPogi()) {
            return $next($request);
        }

        // 1. Isolasi Ketat untuk ADMIN_CABANG
        if ($user->isAdminCabang()) {
            $userBranchId = $user->branch_id;
            
            // Periksa jika ada parameter cabang di request
            $requestedBranchId = $request->input('branch_id') ?? $request->route('branch_id');

            if ($requestedBranchId && $userBranchId && $requestedBranchId !== $userBranchId) {
                return $this->forbiddenResponse($request, 'ADMIN_CABANG', 'Pengurus Cabang Terkait', 'Anda hanya memiliki izin mengelola data di wilayah cabang Anda sendiri.');
            }

            // Periksa jika ada dokter spesifik yang diakses
            $doctorId = $request->input('id') ?? $request->input('doctor_id') ?? $request->route('id');
            if ($doctorId) {
                $doctor = Doctor::find($doctorId);
                if ($doctor && $doctor->branch_id && $userBranchId && $doctor->branch_id !== $userBranchId) {
                    return $this->forbiddenResponse($request, 'ADMIN_CABANG', 'Pengurus Cabang Terkait', 'Dokter ini terdaftar pada cabang daerah lain di luar yurisdiksi kepengurusan Anda.');
                }
            }
        }

        // 2. Isolasi Mandiri untuk DOKTER (Hanya dapat mengakses data dirinya sendiri)
        if ($user->isDoctor()) {
            $doctorId = $request->input('id') ?? $request->input('doctor_id') ?? $request->route('id');
            if ($doctorId) {
                $doctor = Doctor::find($doctorId);
                // Jika dokter ditemukan di database dan user_id berbeda, tolak akses
                if ($doctor && $doctor->user_id && $doctor->user_id !== $user->id) {
                    return $this->forbiddenResponse($request, 'DOKTER', 'Pemilik Portofolio', 'Sesuai regulasi privasi data STR/SIP, dokter hanya berhak mengakses portofolio dan capaian SKP pribadi.');
                }
            }
        }

        return $next($request);
    }

    private function forbiddenResponse(Request $request, string $userRole, string $requiredRoles, string $detail): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: ' . $detail,
            ], 403);
        }

        return response()->view('errors.403', [
            'userRole'      => $userRole,
            'requiredRoles' => $requiredRoles,
            'message'       => 'Wewenang Akses Terbatas (403 Forbidden)',
            'detail'        => $detail,
        ], 403);
    }
}
