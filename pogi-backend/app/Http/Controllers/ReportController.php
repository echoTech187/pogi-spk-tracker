<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Tampilkan laporan bulanan resmi (Monthly Cutoff Snapshots) dari basis data MySQL.
     */
    public function index(Request $request): View
    {
        $snapshots = DB::table('monthly_cutoff_snapshots')
            ->leftJoin('users', 'monthly_cutoff_snapshots.generated_by_user_id', '=', 'users.id')
            ->select(
                'monthly_cutoff_snapshots.*',
                'users.full_name as generated_by_name'
            )
            ->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->get();

        // Total metrik riil dari basis data
        $totalReports = $snapshots->count();
        $totalDoctors = DB::table('doctors')->count();
        $completeDoctors = DB::table('doctors')->where('status', 'COMPLETE')->count();
        $incompleteDoctors = DB::table('doctors')->where('status', 'INCOMPLETE')->count();
        $pendingDoctors = DB::table('doctors')->where('status', 'PENDING_REVIEW')->count();
        $avgCompliance = $totalDoctors > 0 ? round(($completeDoctors / $totalDoctors) * 100, 1) : 0;

        $periodAgg = DB::table('doctor_skp_periods')
            ->where('is_active', true)
            ->selectRaw('
                SUM(achieved_total_credits) as total_skp,
                SUM(achieved_pelayanan_credits) as pel_skp,
                SUM(achieved_pengabdian_credits) as peng_skp,
                SUM(achieved_pembelajaran_credits) as pemb_skp
            ')
            ->first();

        $totalSkp = round((float) ($periodAgg->total_skp ?? 0), 1);
        $pelSkp = round((float) ($periodAgg->pel_skp ?? 0), 1);
        $pengSkp = round((float) ($periodAgg->peng_skp ?? 0), 1);
        $pembSkp = round((float) ($periodAgg->pemb_skp ?? 0), 1);

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $monthShorts = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $now = Carbon::now();
        $currentYear = (int) $now->year;
        $currentMonth = (int) $now->month;

        $snapshotByMonth = $snapshots
            ->where('period_year', $currentYear)
            ->keyBy('period_month');

        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $snap = $snapshotByMonth->get($m);
            $isCurrent = $m === $currentMonth;
            $targetCredits = round($totalDoctors * 2.67, 2);

            if ($snap) {
                $snapComplete = (int) ($snap->complete_doctors ?? 0);
                $snapIncomplete = (int) ($snap->incomplete_doctors ?? max(0, (int) ($snap->total_doctors ?? $totalDoctors) - $snapComplete));
                $snapUpdate = $snap->created_at
                    ? Carbon::parse($snap->created_at)->format('d M Y, H:i') . ' WIB'
                    : "Snapshot {$monthShorts[$m]} {$currentYear}";

                $monthlyData[] = [
                    'name' => "{$monthNames[$m]} {$currentYear}",
                    'short' => $monthShorts[$m],
                    'target' => $targetCredits,
                    'real' => $isCurrent ? $totalSkp : (float) ($snap->total_verified_skp ?? 0),
                    'complete' => $snapComplete,
                    'pending' => $snapIncomplete,
                    'pel' => $isCurrent ? $pelSkp : 0,
                    'peng' => $isCurrent ? $pengSkp : 0,
                    'pemb' => $isCurrent ? $pembSkp : 0,
                    'update' => $snapUpdate,
                ];
            } elseif ($isCurrent) {
                $monthlyData[] = [
                    'name' => "{$monthNames[$m]} {$currentYear}",
                    'short' => $monthShorts[$m],
                    'target' => $targetCredits,
                    'real' => $totalSkp,
                    'complete' => $completeDoctors,
                    'pending' => $pendingDoctors,
                    'pel' => $pelSkp,
                    'peng' => $pengSkp,
                    'pemb' => $pembSkp,
                    'update' => $now->format('d M Y, H:i') . ' WIB',
                ];
            } else {
                $monthlyData[] = [
                    'name' => "{$monthNames[$m]} {$currentYear}",
                    'short' => $monthShorts[$m],
                    'target' => $targetCredits,
                    'real' => 0,
                    'complete' => 0,
                    'pending' => 0,
                    'pel' => 0,
                    'peng' => 0,
                    'pemb' => 0,
                    'update' => $m < $currentMonth ? 'Belum Ada Snapshot' : "Belum Cutoff (1 {$monthShorts[$m]} {$currentYear})",
                ];
            }
        }

        $metrics = [
            'total_reports' => $totalReports,
            'total_verified_skp' => $totalSkp,
            'total_doctors' => $totalDoctors,
            'complete_doctors' => $completeDoctors,
            'incomplete_doctors' => $incompleteDoctors,
            'pending_doctors' => $pendingDoctors,
            'compliance_rate' => $avgCompliance,
        ];

        // Konteks Data Riil Database untuk Ekspor PDF Resmi (Bebas Hardcode)
        $entryStats = DB::table('skp_entries')
            ->select('domain', 'status', DB::raw('COUNT(*) as entry_count'), DB::raw('COALESCE(SUM(credit_amount), 0) as total_credits'))
            ->groupBy('domain', 'status')
            ->get();

        $domainBreakdown = [
            'PEMBELAJARAN' => ['DONE' => 0, 'SUBMITTED' => 0, 'REJECTED' => 0, 'NOT_SUBMITTED' => 0],
            'PELAYANAN' => ['DONE' => 0, 'SUBMITTED' => 0, 'REJECTED' => 0, 'NOT_SUBMITTED' => 0],
            'PENGABDIAN' => ['DONE' => 0, 'SUBMITTED' => 0, 'REJECTED' => 0, 'NOT_SUBMITTED' => 0],
        ];
        $totalRecords = 0;
        foreach ($entryStats as $row) {
            $dom = strtoupper((string) $row->domain);
            $st = strtoupper((string) $row->status);
            if ($st === 'PENDING') $st = 'NOT_SUBMITTED';
            if ($st === 'VERIFIED') $st = 'DONE';
            if (isset($domainBreakdown[$dom][$st])) {
                $domainBreakdown[$dom][$st] += round((float) $row->total_credits, 1);
            }
            $totalRecords += (int) $row->entry_count;
        }

        $topDoctors = DB::table('doctors')
            ->leftJoin('provinces', 'doctors.province_id', '=', 'provinces.id')
            ->leftJoin('doctor_skp_periods as p', function ($j) {
                $j->on('doctors.id', '=', 'p.doctor_id')->where('p.is_active', true);
            })
            ->whereNull('doctors.deleted_at')
            ->orderByDesc('p.achieved_total_credits')
            ->limit(10)
            ->select(
                'doctors.full_name as name',
                'provinces.name as province',
                'doctors.status',
                DB::raw('COALESCE(p.achieved_total_credits, 0) as total_skp'),
                DB::raw('COALESCE(p.achieved_pembelajaran_credits, 0) as pemb_skp'),
                DB::raw('COALESCE(p.achieved_pelayanan_credits, 0) as pel_skp'),
                DB::raw('COALESCE(p.achieved_pengabdian_credits, 0) as peng_skp')
            )
            ->get();

        $topEvents = DB::table('skp_events')
            ->leftJoin('event_categories', 'skp_events.category_id', '=', 'event_categories.id')
            ->leftJoin('skp_entries', 'skp_events.id', '=', 'skp_entries.event_id')
            ->groupBy('skp_events.id', 'skp_events.title', 'skp_events.event_date', 'skp_events.credit_value', 'event_categories.domain')
            ->orderByDesc(DB::raw('COUNT(DISTINCT skp_entries.doctor_id)'))
            ->limit(8)
            ->select(
                'skp_events.title',
                'skp_events.event_date',
                'skp_events.credit_value',
                'event_categories.domain',
                DB::raw('COUNT(DISTINCT skp_entries.doctor_id) as participants')
            )
            ->get();

        $topProvinces = DB::table('provinces')
            ->leftJoin('doctors', function ($j) {
                $j->on('provinces.id', '=', 'doctors.province_id')->whereNull('doctors.deleted_at');
            })
            ->groupBy('provinces.id', 'provinces.name')
            ->havingRaw('COUNT(doctors.id) > 0')
            ->orderByDesc(DB::raw('COUNT(doctors.id)'))
            ->limit(10)
            ->select(
                'provinces.name',
                DB::raw('COUNT(doctors.id) as doctors_count'),
                DB::raw("SUM(CASE WHEN doctors.status = 'COMPLETE' THEN 1 ELSE 0 END) as complete_count")
            )
            ->get();

        $pdfContext = [
            'metrics' => $metrics,
            'totalRecords' => $totalRecords,
            'domainBreakdown' => $domainBreakdown,
            'topDoctors' => $topDoctors,
            'topEvents' => $topEvents,
            'topProvinces' => $topProvinces,
        ];

        return view('reports', [
            'snapshots' => $snapshots,
            'monthlyData' => $monthlyData,
            'metrics' => $metrics,
            'pdfContext' => $pdfContext,
        ]);
    }
}
