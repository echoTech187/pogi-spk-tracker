<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireRole
{
    /**
     * Memeriksa otorisasi peran pengguna terhadap daftar peran yang diizinkan (RBAC)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // 1. Cek Autentikasi
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi Anda telah berakhir. Silakan masuk kembali.'
                ], 401);
            }
            return redirect()->guest(route('login'))->with('error', 'Silakan masuk terlebih dahulu untuk mengakses sistem.');
        }

        // 2. Cek Status Akun Aktif
        if (!$user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'Akun pengurus Anda berstatus non-aktif. Silakan hubungi Sekretariat PP POGI.');
        }

        // 3. Super Admin bypass (Memiliki wewenang di seluruh modul)
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // 4. Periksa kecocokan peran
        if (!$user->hasRole($roles)) {
            $userRole = strtoupper((string) $user->role);
            $requiredRolesText = implode(', ', $roles);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Akses ditolak: Peran Anda ({$userRole}) tidak memiliki wewenang mengakses modul ini.",
                    'required_roles' => $roles,
                    'user_role' => $userRole
                ], 403);
            }

            // Tampilkan halaman 403 Forbidden khusus POGI
            return response()->view('errors.403', [
                'userRole'      => $userRole,
                'requiredRoles' => $requiredRolesText,
                'message'       => 'Wewenang Akses Terbatas (403 Forbidden)',
                'detail'        => "Halaman ini dikhususkan bagi peran: {$requiredRolesText}. Akun Anda terdaftar sebagai {$userRole}."
            ], 403);
        }

        return $next($request);
    }
}
