<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\DTOs\DashboardSummaryDto;
use App\Domain\Contracts\DashboardRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Doctor;
use App\Infrastructure\Persistence\Eloquent\DoctorSkpPeriod;
use App\Infrastructure\Persistence\Eloquent\PogiBranch;
use App\Infrastructure\Persistence\Eloquent\Province;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EloquentDashboardRepository implements DashboardRepositoryInterface
{
    /**
     * Ambil ringkasan data metrik dashboard langsung dari basis data Eloquent
     */
    public function getDashboardSummary(?string $branchId = null, ?string $picUserId = null): DashboardSummaryDto
    {
        // Resolve branch if specified (UUID, code, or alias like 'dki' / 'jabar')
        $targetBranch = null;
        if ($branchId && $branchId !== 'all') {
            $targetBranch = PogiBranch::where('id', $branchId)
                ->orWhere('branch_code', $branchId)
                ->orWhere('branch_name', 'like', "%{$branchId}%")
                ->first();
        }
        $resolvedBranchId = $targetBranch ? $targetBranch->id : $branchId;

        $doctorQuery = Doctor::query()
            ->when($resolvedBranchId, fn($q) => $q->where('branch_id', $resolvedBranchId))
            ->when($picUserId, fn($q) => $q->where('assigned_pic_id', $picUserId));

        $totalDoctors = (clone $doctorQuery)->count();
        $completeCount = (clone $doctorQuery)->where('status', 'COMPLETE')->count();
        $incompleteCount = (clone $doctorQuery)->where('status', 'INCOMPLETE')->count();
        $pendingCount = (clone $doctorQuery)->where('status', 'PENDING_REVIEW')->count();

        // 3 Subbidang Mandatory Progress via doctor_skp_periods
        $periodAggregates = DB::table('doctor_skp_periods')
            ->join('doctors', 'doctor_skp_periods.doctor_id', '=', 'doctors.id')
            ->when($resolvedBranchId, fn($q) => $q->where('doctors.branch_id', $resolvedBranchId))
            ->when($picUserId, fn($q) => $q->where('doctors.assigned_pic_id', $picUserId))
            ->where('doctor_skp_periods.is_active', true)
            ->selectRaw('
                AVG(CASE WHEN target_total_credits > 0 THEN (achieved_total_credits / target_total_credits) * 100 ELSE 0 END) as avg_progress,
                AVG(CASE WHEN target_pelayanan_credits > 0 THEN (achieved_pelayanan_credits / target_pelayanan_credits) * 100 ELSE 0 END) as avg_pelayanan,
                AVG(CASE WHEN target_pengabdian_credits > 0 THEN (achieved_pengabdian_credits / target_pengabdian_credits) * 100 ELSE 0 END) as avg_pengabdian,
                AVG(CASE WHEN target_pembelajaran_credits > 0 THEN (achieved_pembelajaran_credits / target_pembelajaran_credits) * 100 ELSE 0 END) as avg_pembelajaran,
                AVG(target_pelayanan_credits) as target_pelayanan,
                AVG(target_pengabdian_credits) as target_pengabdian,
                AVG(target_pembelajaran_credits) as target_pembelajaran,
                AVG(target_total_credits) as target_total
            ')
            ->first();

        $avgProgressPct = (int) round($periodAggregates->avg_progress ?? 0);
        $avgPelayananPct = round((float) ($periodAggregates->avg_pelayanan ?? 0), 1);
        $avgPengabdianPct = round((float) ($periodAggregates->avg_pengabdian ?? 0), 1);
        $avgPembelajaranPct = round((float) ($periodAggregates->avg_pembelajaran ?? 0), 1);
        $avgAccumulationPct = round(($avgPelayananPct + $avgPengabdianPct + $avgPembelajaranPct) / 3, 1);
        $targetPelayanan = round((float) ($periodAggregates->target_pelayanan ?? 56.0), 1);
        $targetPengabdian = round((float) ($periodAggregates->target_pengabdian ?? 8.0), 1);
        $targetPembelajaran = round((float) ($periodAggregates->target_pembelajaran ?? 72.0), 1);
        $targetTotal = round((float) ($periodAggregates->target_total ?? 160.0), 1);

        // Province Stats for Interactive Map & Ranking
        $provinceRows = DB::table('provinces')
            ->leftJoin('doctors', function ($join) use ($resolvedBranchId, $picUserId) {
                $join->on('provinces.id', '=', 'doctors.province_id')
                     ->whereNull('doctors.deleted_at');
                if ($resolvedBranchId) {
                    $join->where('doctors.branch_id', '=', $resolvedBranchId);
                }
                if ($picUserId) {
                    $join->where('doctors.assigned_pic_id', '=', $picUserId);
                }
            })
            ->select('provinces.id', 'provinces.name')
            ->selectRaw('COUNT(doctors.id) as doctors_count')
            ->selectRaw("SUM(CASE WHEN doctors.status = 'COMPLETE' THEN 1 ELSE 0 END) as complete_count")
            ->selectRaw("SUM(CASE WHEN doctors.status = 'INCOMPLETE' THEN 1 ELSE 0 END) as incomplete_count")
            ->groupBy('provinces.id', 'provinces.name')
            ->orderBy('provinces.name')
            ->get();

        $provincesData = [];
        foreach ($provinceRows as $row) {
            $docs = (int) $row->doctors_count;
            $comp = (int) ($row->complete_count ?? 0);
            $incomp = (int) ($row->incomplete_count ?? 0);
            $rate = $docs > 0 ? round(($comp / $docs) * 100, 1) : 0.0;

            $provincesData[] = [
                'id' => (string) $row->id,
                'name' => (string) $row->name,
                'doctors' => $docs,
                'complete' => $comp,
                'incomplete' => $incomp,
                'completionRate' => $rate,
            ];
        }

        // Lowest & Highest Provinces (filter provinces with doctors > 0)
        $withDoctors = array_filter($provincesData, fn($p) => $p['doctors'] > 0);

        usort($withDoctors, fn($a, $b) => $a['completionRate'] <=> $b['completionRate'] ?: $b['doctors'] <=> $a['doctors']);
        $lowestProvinces = array_slice(array_map(fn($p) => [
            'name' => $p['name'],
            'total' => $p['doctors'],
            'complete' => $p['complete'],
            'pct' => (int) round($p['completionRate']),
        ], $withDoctors), 0, 5);

        usort($withDoctors, fn($a, $b) => $b['completionRate'] <=> $a['completionRate'] ?: $b['doctors'] <=> $a['doctors']);
        $highestProvinces = array_slice(array_map(fn($p) => [
            'name' => $p['name'],
            'total' => $p['doctors'],
            'complete' => $p['complete'],
            'pct' => (int) round($p['completionRate']),
        ], $withDoctors), 0, 5);

        // Top Provinces ranked by doctor count
        $byDoctors = $provincesData;
        usort($byDoctors, fn($a, $b) => $b['doctors'] <=> $a['doctors']);
        $topProvinces = array_slice(array_map(fn($p) => [
            'name' => $p['name'],
            'doctors' => $p['doctors'],
            'completionRate' => $p['completionRate'],
        ], $byDoctors), 0, 8);

        // Scope options for branch selector
        $scopeOptions = [
            ['id' => 'all', 'name' => 'Semua Wilayah Nasional (38 Provinsi)', 'count' => $totalDoctors],
        ];
        $branches = PogiBranch::withCount('doctors')->orderBy('branch_name')->get();
        foreach ($branches as $branch) {
            $scopeOptions[] = [
                'id' => (string) $branch->id,
                'name' => $branch->branch_name,
                'count' => $branch->doctors_count,
            ];
        }

        $activeScope = $targetBranch ? $targetBranch->id : ($branchId ?: 'all');
        $activeScopeLabel = $targetBranch ? $targetBranch->branch_name : ($picUserId ? 'Dokter Binaan PIC Saya' : 'Semua Wilayah Nasional (38 Provinsi)');
        $recentActivities = $this->getRecentActivitiesFromDatabase($resolvedBranchId, $picUserId);

        return new DashboardSummaryDto(
            totalDoctors: $totalDoctors,
            avgProgressPct: $avgProgressPct,
            completeCount: $completeCount,
            incompleteCount: $incompleteCount,
            pendingCount: $pendingCount,
            avgPelayananPct: $avgPelayananPct,
            avgPengabdianPct: $avgPengabdianPct,
            avgPembelajaranPct: $avgPembelajaranPct,
            avgAccumulationPct: $avgAccumulationPct,
            lowestProvinces: $lowestProvinces,
            highestProvinces: $highestProvinces,
            provincesData: $provincesData,
            topProvinces: $topProvinces,
            scopeOptions: $scopeOptions,
            recentActivities: $recentActivities,
            activeScope: $activeScope,
            activeScopeLabel: $activeScopeLabel,
            isFromLiveDatabase: true,
            targetPelayanan: $targetPelayanan,
            targetPengabdian: $targetPengabdian,
            targetPembelajaran: $targetPembelajaran,
            targetTotal: $targetTotal,
        );
    }

    /**
     * Ambil aktivitas terbaru dari logbook SKP di basis data
     */
    private function getRecentActivitiesFromDatabase(?string $branchId, ?string $picUserId = null): array
    {
        try {
            $recentRows = DB::table('skp_entries')
                ->join('doctors', 'skp_entries.doctor_id', '=', 'doctors.id')
                ->leftJoin('users', 'skp_entries.verified_by_user_id', '=', 'users.id')
                ->when($branchId, fn($q) => $q->where('doctors.branch_id', $branchId))
                ->when($picUserId, fn($q) => $q->where('doctors.assigned_pic_id', $picUserId))
                ->orderByDesc('skp_entries.created_at')
                ->limit(5)
                ->select([
                    'skp_entries.title',
                    'skp_entries.domain',
                    'skp_entries.credit_amount',
                    'skp_entries.status',
                    'skp_entries.created_at',
                    'doctors.full_name as doctor_name',
                    'users.full_name as verifier_name',
                ])
                ->get();

            if ($recentRows->isNotEmpty()) {
                return $recentRows->map(function ($row) {
                    $isVerified = $row->status === 'DONE';
                    $actor = $row->verifier_name ?: ($isVerified ? 'Verifikator POGI' : $row->doctor_name);
                    $action = $isVerified ? 'Verifikasi Kredit SKP' : 'Pengajuan Berkas SKP';
                    $category = $isVerified ? 'Verification' : 'Submission';

                    return [
                        'actor' => $actor,
                        'action' => $action,
                        'details' => "{$row->title} ({$row->credit_amount} SKP {$row->domain}) - {$row->doctor_name}",
                        'timestamp' => Carbon::parse($row->created_at)->diffForHumans(),
                        'category' => $category,
                    ];
                })->toArray();
            }
        } catch (\Throwable $e) {}

        return [];
    }
}
