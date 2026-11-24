<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login resmi
     */
    public function showLoginForm()
    {
        try {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user && $user->isDoctor()) {
                    return redirect()->route('doctors.show', ['id' => 'doc-001']);
                }
                return redirect()->route('dashboard');
            }
        } catch (\Throwable $e) {
            session()->flush();
        }

        return view('login');
    }

    /**
     * Proses autentikasi login pengguna
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $email = strtolower(trim($credentials['email']));
        $password = $credentials['password'];

        // Proteksi Brute-force (Rate Limiting: Maks 5 percobaan per menit)
        $throttleKey = Str::transliterate($email . '|' . $request->ip());
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => 'Terlalu banyak percobaan masuk yang gagal. Silakan coba kembali dalam ' . $seconds . ' detik.',
            ])->withInput($request->only('email'));
        }

        // 1. Cari user di basis data resmi POGI
        $user = User::where('email', $email)->first();

        if (!$user) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors([
                'email' => 'ID Pengurus / Email tidak terdaftar dalam basis data resmi POGI.',
            ])->withInput($request->only('email'));
        }

        // 2. Verifikasi status keaktifan akun
        if (!$user->is_active) {
            return back()->withErrors([
                'email' => 'Akun pengurus ini berstatus non-aktif. Silakan hubungi Sekretariat PP POGI.',
            ])->withInput($request->only('email'));
        }

        // 3. Verifikasi Password menggunakan Bcrypt standar Laravel
        if (!Hash::check($password, $user->getAuthPassword())) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors([
                'password' => 'Kata sandi yang Anda masukkan salah.',
            ])->withInput($request->only('email'));
        }

        // Login berhasil, bersihkan riwayat percobaan gagal
        RateLimiter::clear($throttleKey);

        // 4. Lakukan Login Session secara aman
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // 5. Catat waktu aktivitas login terakhir
        try {
            $user->update(['last_login_at' => now()]);
        } catch (\Throwable $e) {}

        // 6. Arahkan pengguna berdasarkan peran resmi (Role-based redirection)
        if ($user->isDoctor()) {
            return redirect()->route('doctors.show', ['id' => 'doc-001'])
                ->with('success', 'Selamat datang di Portal Mandiri Dokter Sp.OG: ' . $user->name);
        }

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Berhasil masuk ke Konsol Nasional sebagai ' . ($user->role ?? 'Pengurus'));
    }

    /**
     * Logout dari sistem
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->forget(['mock_user_role', 'mock_user_name', 'mock_user_email', 'mock_user_branch_id']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah berhasil keluar dari sistem POGI SKP Tracker.');
    }

    /**
     * Beralih konteks peran aktif secara cepat (Fitur Evaluasi & Uji RBAC)
     */
    public function switchRole(Request $request)
    {
        // Hanya diizinkan untuk lingkungan lokal/testing atau pengguna yang sudah terautentikasi sebagai SUPER_ADMIN
        if (!app()->environment('local', 'testing') && (!Auth::check() || !Auth::user()->isSuperAdmin())) {
            abort(403, 'Akses ditolak: Fitur peralihan peran hanya dapat diakses oleh Super Administrator.');
        }

        $targetRole = strtoupper($request->input('role', 'SUPER_ADMIN'));
        
        $user = User::where('role', $targetRole)->first();

        if (!$user) {
            return redirect()->back()->withErrors(['error' => 'Pengguna dengan peran ' . $targetRole . ' tidak ditemukan dalam basis data.']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->isDoctor()) {
            return redirect()->route('doctors.show', ['id' => 'doc-001'])
                ->with('info', 'Beralih ke konteks: User / Dokter Spesialis Obgyn');
        }

        return redirect()->back()->with('info', 'Beralih ke konteks: ' . $targetRole);
    }
}
