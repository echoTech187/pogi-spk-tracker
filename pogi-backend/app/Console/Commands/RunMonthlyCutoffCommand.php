<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RunMonthlyCutoffCommand extends Command
{
    protected $signature = 'pogi:monthly-cutoff {--year=} {--month=}';
    protected $description = 'Eksekusi Monthly Cutoff Snapshot setiap tanggal 1 pukul 00:00 WIB (RULES.md Bab 3)';

    public function handle(): int
    {
        $now = Carbon::now('Asia/Jakarta');
        $year = (int) ($this->option('year') ?: $now->year);
        $month = (int) ($this->option('month') ?: $now->month);

        $exists = DB::table('monthly_cutoff_snapshots')
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->where('is_locked', true)
            ->exists();

        if ($exists) {
            $this->info("Snapshot cutoff {$year}-{$month} sudah terkunci.");
            return self::SUCCESS;
        }

        $totalDoctors = DB::table('doctors')->count();
        $completeDoctors = DB::table('doctors')->where('status', 'COMPLETE')->count();
        $incompleteDoctors = max(0, $totalDoctors - $completeDoctors);
        $complianceRate = $totalDoctors > 0 ? round(($completeDoctors / $totalDoctors) * 100, 2) : 0;

        $totalVerifiedSkp = (float) DB::table('doctor_skp_periods')
            ->where('is_active', true)
            ->sum('achieved_total_credits');

        $snapshotId = (string) Str::uuid();
        $reportFile = sprintf('Monthly_Report_POGI_%04d_%02d.pdf', $year, $month);

        DB::table('monthly_cutoff_snapshots')->insert([
            'id' => $snapshotId,
            'period_year' => $year,
            'period_month' => $month,
            'cutoff_timestamp' => $now,
            'total_doctors' => $totalDoctors,
            'complete_doctors' => $completeDoctors,
            'incomplete_doctors' => $incompleteDoctors,
            'compliance_percentage' => $complianceRate,
            'total_verified_skp' => $totalVerifiedSkp,
            'report_pdf_path' => "reports/{$reportFile}",
            'report_sha256_checksum' => hash('sha256', "{$snapshotId}|{$year}|{$month}|{$totalDoctors}|{$completeDoctors}"),
            'is_locked' => true,
            'generated_by_user_id' => DB::table('users')->where('role', 'SUPER_ADMIN')->value('id'),
            'created_at' => $now,
        ]);

        // Simpan detail per dokter pada saat cutoff
        $doctors = DB::table('doctors')
            ->leftJoin('doctor_skp_periods as p', function ($j) {
                $j->on('doctors.id', '=', 'p.doctor_id')->where('p.is_active', true);
            })
            ->select(
                'doctors.id as doctor_id',
                'doctors.status',
                DB::raw('COALESCE(p.achieved_total_credits, 0) as total_credits')
            )
            ->get();

        $detailRows = [];
        foreach ($doctors as $doc) {
            $detailRows[] = [
                'id' => (string) Str::uuid(),
                'snapshot_id' => $snapshotId,
                'doctor_id' => $doc->doctor_id,
                'achieved_credits_at_cutoff' => (float) $doc->total_credits,
                'compliance_status_at_cutoff' => $doc->status ?: 'INCOMPLETE',
                'created_at' => $now,
            ];
        }
        if (!empty($detailRows)) {
            foreach (array_chunk($detailRows, 100) as $chunk) {
                DB::table('monthly_doctor_cutoff_details')->insert($chunk);
            }
        }

        DB::table('audit_logs')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => null,
            'action' => 'CRON_MONTHLY_CUTOFF',
            'entity_name' => 'monthly_cutoff_snapshots',
            'entity_id' => $snapshotId,
            'new_values' => json_encode([
                'message' => "Monthly Cutoff {$year}-{$month} terkunci: {$completeDoctors}/{$totalDoctors} Complete ({$complianceRate}%).",
            ]),
            'user_agent' => 'Laravel Scheduler (pogi:monthly-cutoff)',
            'created_at' => $now,
        ]);

        $this->info("Monthly cutoff {$year}-{$month} selesai & terkunci ({$completeDoctors}/{$totalDoctors} dokter Complete).");
        return self::SUCCESS;
    }
}
