<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - POGI SKP Tracker
|--------------------------------------------------------------------------
| Seluruh antarmuka mempertahankan 100% desain asli, styling Tailwind,
| komponen Leaflet Maps, ApexCharts, Modal dialog, dan interaktivitas
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/doctors', function () {
    return view('doctors');
})->name('doctors.index');

Route::get('/doctor-detail', function () {
    return view('doctor-detail');
})->name('doctors.show');

Route::get('/provinces', function () {
    return view('provinces');
})->name('provinces');

Route::get('/events', function () {
    return view('events');
})->name('events');

Route::get('/activity', function () {
    return view('activity');
})->name('activity');

Route::get('/reports', function () {
    return view('reports');
})->name('reports');

Route::get('/admin-performance', function () {
    return view('admin-performance');
})->name('admin-performance');

Route::get('/manage', function () {
    return view('manage');
})->name('manage');

Route::get('/login', function () {
    return view('login');
})->name('login');
