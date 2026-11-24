<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - POGI SKP Tracker
|--------------------------------------------------------------------------
| Seluruh alur autentikasi dan otorisasi terintegrasi dengan Matriks RBAC
| 5 Peran Resmi Sistem, Isolasi Multi-Tenant Cabang, & Middleware Otorisasi
|--------------------------------------------------------------------------
*/

// =========================================================================
// 1. RUTE PUBLIK & AUTENTIKASI (GUEST)
// =========================================================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/switch-role', [AuthController::class, 'switchRole'])->name('switch-role');

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// =========================================================================
// 2. RUTE TERPROTEKSI (AUTH + ACTIVE USER + RBAC MIDDLEWARE)
// =========================================================================
Route::middleware(['active'])->group(function () {

    // 2.1 Dashboard Nasional (Dapat diakses oleh Super Admin, Pusat, Verifikator, & Cabang)
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('role:SUPER_ADMIN,ADMIN_PUSAT,ADMIN_POGI,ADMIN_CABANG');

    // 2.2 Direktori Dokter (Dengan Isolasi Cabang Otomatis untuk ADMIN_CABANG)
    Route::get('/doctors', function () {
        return view('doctors');
    })->name('doctors.index')->middleware(['role:SUPER_ADMIN,ADMIN_PUSAT,ADMIN_POGI,ADMIN_CABANG', 'branch.scope']);

    // 2.3 Portofolio Detail Dokter (Semua role termasuk USER_DOKTER untuk akses mandiri)
    Route::get('/doctor-detail', function () {
        return view('doctor-detail');
    })->name('doctors.show')->middleware(['role:SUPER_ADMIN,ADMIN_PUSAT,ADMIN_POGI,ADMIN_CABANG,USER_DOKTER', 'branch.scope']);

    // 2.4 Peta Provinsi & Katalog Events
    Route::get('/provinces', function () {
        return view('provinces');
    })->name('provinces')->middleware('role:SUPER_ADMIN,ADMIN_PUSAT,ADMIN_POGI,ADMIN_CABANG');

    Route::get('/events', function () {
        return view('events');
    })->name('events')->middleware('role:SUPER_ADMIN,ADMIN_PUSAT,ADMIN_POGI,ADMIN_CABANG');

    // 2.5 Laporan Bulanan (Super Admin, Admin Pusat, Admin Cabang)
    Route::get('/reports', function () {
        return view('reports');
    })->name('reports')->middleware('role:SUPER_ADMIN,ADMIN_PUSAT,ADMIN_CABANG');

    // 2.6 Monitoring SLA & Performa Admin (Khusus Super Admin & Admin Pusat)
    Route::get('/admin-performance', function () {
        return view('admin-performance');
    })->name('admin-performance')->middleware('role:SUPER_ADMIN,ADMIN_PUSAT');

    // 2.7 Log Aktivitas & Jejak Audit (Super Admin & Admin Pusat)
    Route::get('/activity', function () {
        return view('activity');
    })->name('activity')->middleware('role:SUPER_ADMIN,ADMIN_PUSAT');

    // 2.8 Pengaturan Master Data (Eksklusif SUPER_ADMIN - RULES.md Bab 7)
    Route::get('/manage', function () {
        return view('manage');
    })->name('manage')->middleware('role:SUPER_ADMIN');
});
