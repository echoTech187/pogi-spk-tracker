<?php

namespace App\Http\Controllers;

use App\Domain\Entities\SystemRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProvinceController extends Controller
{
    /**
     * Tampilkan data capaian kepatuhan 38 provinsi secara live dari basis data MySQL.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $roleStr = $user?->role instanceof SystemRole ? $user->role->value : strtoupper((string) ($user?->role ?? ''));
        $picUserId = $roleStr === 'ADMIN_POGI' ? $user->id : null;
        $branchId = $roleStr === 'ADMIN_CABANG' ? $user->branch_id : null;

        $query = DB::table('provinces')
            ->leftJoin('doctors', function ($join) use ($picUserId, $branchId) {
                $join->on('provinces.id', '=', 'doctors.province_id')
                     ->whereNull('doctors.deleted_at');
                if ($picUserId) {
                    $join->where('doctors.assigned_pic_id', '=', $picUserId);
                }
                if ($branchId) {
                    $join->where('doctors.branch_id', '=', $branchId);
                }
            })
            ->select('provinces.id', 'provinces.name', 'provinces.region_island')
            ->selectRaw('COUNT(doctors.id) as doctors_count')
            ->selectRaw("SUM(CASE WHEN doctors.status = 'COMPLETE' THEN 1 ELSE 0 END) as complete_count")
            ->selectRaw("SUM(CASE WHEN doctors.status = 'INCOMPLETE' THEN 1 ELSE 0 END) as incomplete_count")
            ->selectRaw("SUM(CASE WHEN doctors.status = 'PENDING_REVIEW' THEN 1 ELSE 0 END) as pending_count")
            ->groupBy('provinces.id', 'provinces.name', 'provinces.region_island');

        if ($picUserId || $branchId) {
            $query->havingRaw('COUNT(doctors.id) > 0');
        }

        $provinceRows = $query
            ->orderBy('provinces.name')
            ->get();

        $provincesData = [];
        $totalDoctorsAll = 0;
        $totalCompleteAll = 0;
        $totalIncompleteAll = 0;

        foreach ($provinceRows as $p) {
            $docs = (int) $p->doctors_count;
            $comp = (int) ($p->complete_count ?? 0);
            $incomp = (int) ($p->incomplete_count ?? 0);
            $rate = $docs > 0 ? round(($comp / $docs) * 100, 1) : 0.0;

            $totalDoctorsAll += $docs;
            $totalCompleteAll += $comp;
            $totalIncompleteAll += $incomp;

            $provincesData[] = [
                'id' => (string) $p->id,
                'name' => (string) $p->name,
                'region' => (string) ($p->region_island ?: 'Wilayah Nasional'),
                'doctors' => $docs,
                'complete' => $comp,
                'incomplete' => $incomp,
                'pending' => (int) ($p->pending_count ?? 0),
                'completionRate' => $rate,
            ];
        }

        // Urutkan provinsi berdasarkan completion rate tertinggi untuk peringkat
        $rankedProvinces = $provincesData;
        usort($rankedProvinces, fn($a, $b) => $b['completionRate'] <=> $a['completionRate'] ?: $b['doctors'] <=> $a['doctors']);

        $topProvince = !empty($rankedProvinces) ? $rankedProvinces[0] : null;
        $lowestProvinces = array_values(array_filter($rankedProvinces, fn($p) => $p['doctors'] > 0));
        $lowestProvince = !empty($lowestProvinces) ? end($lowestProvinces) : null;

        $nationalSummary = [
            'total_provinces' => count($provincesData),
            'total_doctors' => $totalDoctorsAll,
            'total_complete' => $totalCompleteAll,
            'total_incomplete' => $totalIncompleteAll,
            'avg_compliance' => $totalDoctorsAll > 0 ? round(($totalCompleteAll / $totalDoctorsAll) * 100, 1) : 0,
            'top_province' => $topProvince ? $topProvince['name'] . ' (' . $topProvince['completionRate'] . '%)' : '-',
            'lowest_province' => $lowestProvince ? $lowestProvince['name'] . ' (' . $lowestProvince['completionRate'] . '%)' : '-',
        ];

        return view('provinces', [
            'provinces' => $provincesData,
            'rankedProvinces' => $rankedProvinces,
            'nationalSummary' => $nationalSummary,
        ]);
    }
}
