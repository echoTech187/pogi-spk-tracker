<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBranchAccess
{
    /**
     * Membatasi Admin Cabang agar HANYA dapat mengakses dokter di cabangnya sendiri (Isolasi Multi-Tenant)
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->guest(route('login'));
        }

        // Peran Nasional (Super Admin, Admin Pusat, Admin POGI) tidak dibatasi isolasi cabang
        if ($user->isSuperAdmin() || $user->isAdminPusat() || $user->isAdminPogi()) {
            return $next($request);
        }

        // Isolasi Ketat untuk ADMIN_CABANG
        if ($user->isAdminCabang()) {
            $userBranchId = $user->branch_id;
            
            // Periksa jika ada parameter cabang di request
            $requestedBranchId = $request->input('branch_id') ?? $request->route('branch_id');

            if ($requestedBranchId && $userBranchId && $requestedBranchId !== $userBranchId) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akses ditolak: Anda hanya memiliki izin mengelola data di wilayah cabang Anda sendiri.'
                    ], 403);
                }

                return response()->view('errors.403', [
                    'userRole'      => 'ADMIN_CABANG',
                    'requiredRoles' => 'Pengurus Cabang Terkait',
                    'message'       => 'Pelanggaran Isolasi Wilayah Cabang (403 Forbidden)',
                    'detail'        => 'Sesuai regulasi sistem, Admin Cabang hanya diizinkan melihat dan memverifikasi berkas dokter di wilayah cabangnya sendiri.'
                ], 403);
            }
        }

        return $next($request);
    }
}
