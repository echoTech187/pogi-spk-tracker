<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// RULES.md Bab 3: Monthly Cutoff Snapshot setiap tanggal 1 pukul 00:00 WIB
Schedule::command('pogi:monthly-cutoff')
    ->monthlyOn(1, '00:00')
    ->timezone('Asia/Jakarta');

// RULES.md Bab 4: Pengingat otomatis H-7 (tanggal 24) & H-2 (tanggal 28) pukul 08:00 WIB
Schedule::command('pogi:wa-reminder --trigger=H-7')
    ->monthlyOn(24, '08:00')
    ->timezone('Asia/Jakarta');

Schedule::command('pogi:wa-reminder --trigger=H-2')
    ->monthlyOn(28, '08:00')
    ->timezone('Asia/Jakarta');

// RULES.md Bab 4: Worker pemroses antrean WA maksimal 15 pesan/menit
Schedule::command('pogi:wa-process-queue --limit=15')
    ->everyMinute()
    ->withoutOverlapping();
