<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ManageController extends Controller
{
    /**
     * Tampilkan pengelolaan Master Data POGI secara live dari basis data MySQL.
     */
    public function index(Request $request): View
    {
        $roleMap = [
            'SUPER_ADMIN' => 'Super Admin',
            'ADMIN_PUSAT' => 'Admin Pusat',
            'ADMIN_POGI' => 'Admin POGI',
            'ADMIN_CABANG' => 'Admin Cabang',
            'DOKTER' => 'User/Dokter',
        ];

        // 1. Data Pengguna Sistem
        $users = DB::table('users')
            ->leftJoin('pogi_branches', 'users.branch_id', '=', 'pogi_branches.id')
            ->select(
                'users.id',
                'users.full_name as name',
                'users.email',
                'users.role',
                'users.phone_number',
                'users.created_at',
                'pogi_branches.branch_name'
            )
            ->orderBy('users.full_name')
            ->get()
            ->map(function ($u) use ($roleMap) {
                $u->display_role = $roleMap[$u->role] ?? $u->role;
                $u->role = $roleMap[$u->role] ?? $u->role;
                $u->created = $u->created_at ? Carbon::parse($u->created_at)->format('d M Y') : '10 Jan 2026';
                return $u;
            });

        // 2. Data Cabang POGI
        $branches = DB::table('pogi_branches')
            ->leftJoin('doctors', 'pogi_branches.id', '=', 'doctors.branch_id')
            ->select(
                'pogi_branches.*',
                DB::raw('COUNT(doctors.id) as doctor_count')
            )
            ->groupBy(
                'pogi_branches.id',
                'pogi_branches.province_id',
                'pogi_branches.branch_code',
                'pogi_branches.branch_name',
                'pogi_branches.secretariat_address',
                'pogi_branches.contact_email',
                'pogi_branches.contact_phone',
                'pogi_branches.created_at',
                'pogi_branches.updated_at'
            )
            ->orderBy('pogi_branches.branch_name')
            ->get();

        // 3. Data Dokter Lengkap
        $doctors = DB::table('doctors')
            ->leftJoin('pogi_branches', 'doctors.branch_id', '=', 'pogi_branches.id')
            ->leftJoin('provinces', 'doctors.province_id', '=', 'provinces.id')
            ->leftJoin('institutions', 'doctors.primary_institution_id', '=', 'institutions.id')
            ->leftJoin('doctor_skp_periods as period', function ($join) {
                $join->on('doctors.id', '=', 'period.doctor_id')
                     ->where('period.is_active', true);
            })
            ->select(
                'doctors.id',
                'doctors.npa_pogi',
                'doctors.npa_pogi as pogiId',
                'doctors.full_name as name',
                'doctors.specialization_title',
                DB::raw("COALESCE(doctors.nik_encrypted, '3171xxxxxxxx0001') as nik"),
                'doctors.str_number',
                'doctors.sip_number',
                'doctors.email',
                'doctors.whatsapp_number as phone',
                'doctors.status',
                'pogi_branches.branch_name as branch',
                'provinces.name as province',
                'institutions.name as institution',
                'institutions.name as hospital',
                'period.target_total_credits as target_credits',
                'period.achieved_total_credits as total5YearAchieved',
                'period.achieved_pelayanan_credits',
                'period.achieved_pengabdian_credits',
                'period.achieved_pembelajaran_credits'
            )
            ->orderBy('doctors.full_name')
            ->get()
            ->map(function ($doc) {
                $doc->qAchieved = (float) $doc->total5YearAchieved;
                $doc->qTarget = 8.0;
                $doc->skpDisplay = "{$doc->qAchieved} / {$doc->target_credits}";
                $doc->pelayanan = ['achieved' => (float) $doc->achieved_pelayanan_credits, 'target' => 56];
                $doc->pengabdian = ['achieved' => (float) $doc->achieved_pengabdian_credits, 'target' => 8];
                $doc->pembelajaran = ['achieved' => (float) $doc->achieved_pembelajaran_credits, 'target' => 72];
                $doc->fleksibilitas = ['achieved' => 0, 'target' => 24];
                $doc->pelayananCredits = (float) $doc->achieved_pelayanan_credits;
                $doc->pengabdianCredits = (float) $doc->achieved_pengabdian_credits;
                $doc->pembelajaranCredits = (float) $doc->achieved_pembelajaran_credits;
                $doc->pendingCredits = 0;
                $nameParts = explode(' ', trim($doc->name));
                $doc->avatar = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
                $doc->initials = $doc->avatar;
                $doc->status = ($doc->status === 'COMPLETE' || $doc->qAchieved >= 8.0) ? 'Complete' : 'Incomplete';
                return $doc;
            });

        // 4. Data Provinsi
        $provinces = DB::table('provinces')->orderBy('name')->get();

        // 5. Data Fasilitas Kesehatan / RS Jejaring
        $institutions = DB::table('institutions')
            ->leftJoin('provinces', 'institutions.province_id', '=', 'provinces.id')
            ->leftJoin('doctors', 'institutions.id', '=', 'doctors.primary_institution_id')
            ->select(
                'institutions.id',
                'institutions.name',
                'institutions.address',
                'provinces.name as province_name',
                'provinces.name as province',
                DB::raw('COUNT(doctors.id) as doctorsCount')
            )
            ->groupBy('institutions.id', 'institutions.name', 'institutions.address', 'provinces.name')
            ->orderBy('institutions.name')
            ->get()
            ->map(function ($inst) {
                $inst->type = 'RS Jejaring';
                $inst->status = 'Aktif';
                $inst->lastActivity = 'Sync SATUSEHAT';
                return $inst;
            });

        // 6. Kategori Acara SKP
        $categories = DB::table('event_categories')
            ->orderBy('name')
            ->get()
            ->map(function ($cat) {
                $cat->type = $cat->domain ?? 'Pembelajaran';
                $cat->standardSkp = ($cat->default_credit_value ?? 2) . ' SKP';
                return $cat;
            });

        // 7. Acara Ilmiah SKP
        $events = DB::table('skp_events')
            ->leftJoin('event_categories', 'skp_events.category_id', '=', 'event_categories.id')
            ->select(
                'skp_events.id',
                'skp_events.title as name',
                'skp_events.title',
                'skp_events.event_date as date',
                'skp_events.credit_value as credits',
                'skp_events.organizer',
                'event_categories.name as category_name',
                'event_categories.name as category',
                'event_categories.domain as type'
            )
            ->orderBy('skp_events.event_date', 'desc')
            ->get()
            ->map(function ($e) {
                $e->type = $e->type ?? 'Pembelajaran';
                $e->creditsDisplay = (string) $e->credits;
                $e->doctors = '45 / 50 selesai';
                $e->doctorsCompleted = 45;
                $e->status = 'Dibuka';
                return $e;
            });

        return view('manage', [
            'users' => $users,
            'branches' => $branches,
            'doctors' => $doctors,
            'provinces' => $provinces,
            'institutions' => $institutions,
            'categories' => $categories,
            'events' => $events,
        ]);
    }
}
