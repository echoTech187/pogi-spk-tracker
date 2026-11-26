<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AdminPerformanceController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\KemenkesController;
use App\Http\Controllers\ManageController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\ReportController;
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
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:15,1')->name('login.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/switch-role', [AuthController::class, 'switchRole'])->middleware(['auth', 'role:SUPER_ADMIN'])->name('switch-role');

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// =========================================================================
// 2. RUTE TERPROTEKSI (AUTH + ACTIVE USER + RBAC MIDDLEWARE)
// =========================================================================
Route::middleware(['auth', 'active'])->group(function () {

    // 2.1 Dashboard Nasional (Dapat diakses oleh Super Admin, Pusat, Verifikator, & Cabang)
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('role:SUPER_ADMIN,ADMIN_PUSAT,ADMIN_POGI,ADMIN_CABANG');
    Route::post('/dashboard/send-follow-up', [DashboardController::class, 'sendFollowUp'])
        ->name('dashboard.followup')
        ->middleware('role:SUPER_ADMIN,ADMIN_POGI');

    // 2.2 Sesi Kemenkes Tracker (Auto-Log Aktivitas Kinerja Admin ke DB)
    Route::post('/kemenkes/track-access', [KemenkesController::class, 'trackAccess'])
        ->name('kemenkes.track');
    Route::get('/kemenkes/launch', [KemenkesController::class, 'launchAndTrack'])
        ->name('kemenkes.launch');

    // 2.3 Direktori Dokter (Dengan Isolasi Cabang Otomatis untuk ADMIN_CABANG)
    Route::get('/doctors', [DoctorController::class, 'index'])
        ->name('doctors.index')
        ->middleware(['role:SUPER_ADMIN,ADMIN_PUSAT,ADMIN_POGI,ADMIN_CABANG', 'branch.scope']);
    Route::post('/doctors', [DoctorController::class, 'store'])
        ->name('doctors.store')
        ->middleware('role:SUPER_ADMIN,ADMIN_POGI');

    // 2.4 Portofolio Detail Dokter & Mutasi Data Live
    Route::get('/doctor-detail', [DoctorController::class, 'show'])
        ->name('doctors.show')
        ->middleware(['role:SUPER_ADMIN,ADMIN_PUSAT,ADMIN_POGI,ADMIN_CABANG,USER_DOKTER,DOKTER', 'branch.scope']);
    Route::post('/doctors/{id}/verify-credits', [DoctorController::class, 'verifyCredits'])
        ->name('doctors.verify-credits')
        ->middleware('role:SUPER_ADMIN,ADMIN_POGI');
    Route::post('/doctors/{id}/documents', [DoctorController::class, 'uploadDocument'])
        ->name('doctors.upload-doc')
        ->middleware('role:SUPER_ADMIN,ADMIN_POGI,USER_DOKTER,DOKTER');
    Route::post('/doctors/{id}/documents/{docId}/status', [DoctorController::class, 'updateDocumentStatus'])
        ->name('doctors.update-doc-status')
        ->middleware('role:SUPER_ADMIN,ADMIN_POGI');

    // 2.5 Ubah & Tentukan PIC Pengelola Dokter (Eksklusif SUPER_ADMIN - Bu Dian, Bp Ade, Bp Ircham)
    Route::post('/doctors/batch-assign-pic', [DoctorController::class, 'batchAssignPic'])
        ->name('doctors.batch-assign-pic')
        ->middleware('role:SUPER_ADMIN');
    Route::post('/doctors/{id}/assign-pic', [DoctorController::class, 'assignPic'])
        ->name('doctors.assign-pic')
        ->middleware('role:SUPER_ADMIN');

    // 2.6 Peta Provinsi & Katalog Events
    Route::get('/provinces', [ProvinceController::class, 'index'])
        ->name('provinces')
        ->middleware('role:SUPER_ADMIN,ADMIN_PUSAT,ADMIN_POGI,ADMIN_CABANG');

    Route::get('/events', [EventController::class, 'index'])
        ->name('events')
        ->middleware('role:SUPER_ADMIN,ADMIN_PUSAT,ADMIN_POGI,ADMIN_CABANG');

    // 2.7 Laporan Bulanan (Super Admin, POGI Pusat, Admin Cabang)
    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports')
        ->middleware('role:SUPER_ADMIN,ADMIN_PUSAT,ADMIN_CABANG');

    // 2.8 Monitoring SLA & Performa Admin (Super Admin, POGI Pusat, Admin POGI)
    Route::get('/admin-performance', [AdminPerformanceController::class, 'index'])
        ->name('admin-performance')
        ->middleware('role:SUPER_ADMIN,ADMIN_PUSAT,ADMIN_POGI');

    // 2.9 Log Aktivitas & Jejak Audit (Super Admin & POGI Pusat)
    Route::get('/activity', [ActivityController::class, 'index'])
        ->name('activity')
        ->middleware('role:SUPER_ADMIN,ADMIN_PUSAT');
    Route::post('/activity/cron-simulation', [ActivityController::class, 'simulateCron'])
        ->name('activity.cron-simulation')
        ->middleware('role:SUPER_ADMIN,ADMIN_PUSAT');

    // 2.10 Pengaturan Master Data (Eksklusif SUPER_ADMIN - RULES.md Bab 7)
    Route::get('/manage', [ManageController::class, 'index'])
        ->name('manage')
        ->middleware('role:SUPER_ADMIN');
    Route::post('/manage/store', [ManageController::class, 'store'])
        ->name('manage.store')
        ->middleware('role:SUPER_ADMIN');
    Route::post('/manage/import', [ManageController::class, 'import'])
        ->name('manage.import')
        ->middleware('role:SUPER_ADMIN');
    Route::post('/manage/users/{id}/status', [ManageController::class, 'updateUserStatus'])
        ->name('manage.users.status')
        ->middleware('role:SUPER_ADMIN');
    Route::post('/manage/{type}/{id}/update', [ManageController::class, 'update'])
        ->name('manage.update')
        ->middleware('role:SUPER_ADMIN');
    Route::delete('/manage/{type}/{id}', [ManageController::class, 'destroy'])
        ->name('manage.destroy')
        ->middleware('role:SUPER_ADMIN');
});
