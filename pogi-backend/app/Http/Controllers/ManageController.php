<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
        $eventCompletions = DB::table('skp_entries')
            ->select('event_id', DB::raw('COUNT(DISTINCT doctor_id) as done_count'))
            ->whereNotNull('event_id')
            ->groupBy('event_id')
            ->pluck('done_count', 'event_id');

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
            ->map(function ($e) use ($eventCompletions) {
                $doneCount = (int) ($eventCompletions[$e->id] ?? 0);
                $e->type = $e->type ?? 'Pembelajaran';
                $e->creditsDisplay = (string) $e->credits;
                $e->doctors = "{$doneCount} selesai";
                $e->doctorsCompleted = $doneCount;
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

    /**
     * Simpan data master baru (Dokter, Institusi, Event, Kategori, atau User) ke basis data.
     */
    public function store(Request $request): JsonResponse
    {
        $entityType = $request->input('entity_type');
        $now = Carbon::now();
        $actorId = Auth::id();

        if ($entityType === 'doctor') {
            $v = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'nik' => ['nullable', 'string', 'max:64'],
                'province' => ['required', 'string', 'max:120'],
                'institution' => ['nullable', 'string', 'max:255'],
                'phone' => ['nullable', 'string', 'max:40'],
            ]);

            $provinceRow = DB::table('provinces')->where('name', 'like', '%' . $v['province'] . '%')->first()
                ?? DB::table('provinces')->first();
            $branchRow = $provinceRow
                ? (DB::table('pogi_branches')->where('province_id', $provinceRow->id)->first() ?? DB::table('pogi_branches')->first())
                : DB::table('pogi_branches')->first();
            $instRow = !empty($v['institution'])
                ? DB::table('institutions')->where('name', 'like', '%' . $v['institution'] . '%')->first()
                : null;

            $doctorId = (string) Str::uuid();
            $npa = '#POGI-' . $now->year . '-' . rand(2000, 9999);
            $provName = $provinceRow->name ?? $v['province'];

            DB::table('doctors')->insert([
                'id' => $doctorId,
                'npa_pogi' => $npa,
                'nik_encrypted' => $v['nik'] ?: ('ENC_' . rand(3200000000000000, 3299999999999999)),
                'str_number' => '31.1.2.' . rand(100, 999) . '.1.26',
                'sip_number' => '446/' . rand(100, 999) . '/SIP/' . $now->year,
                'full_name' => $v['name'],
                'specialization_title' => 'Sp.OG',
                'email' => Str::slug($v['name'], '.') . '@pogi-member.id',
                'whatsapp_number' => $v['phone'] ?: '+6281200000000',
                'province_id' => $provinceRow?->id,
                'branch_id' => $branchRow?->id,
                'primary_institution_id' => $instRow?->id,
                'status' => 'INCOMPLETE',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('doctor_skp_periods')->insert([
                'id' => (string) Str::uuid(),
                'doctor_id' => $doctorId,
                'period_start_date' => $now->copy()->startOfYear()->toDateString(),
                'period_end_date' => $now->copy()->addYears(5)->startOfYear()->toDateString(),
                'target_total_credits' => 160.00,
                'achieved_total_credits' => 0,
                'achieved_pelayanan_credits' => 0,
                'achieved_pengabdian_credits' => 0,
                'achieved_pembelajaran_credits' => 0,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $item = [
                'id' => $doctorId,
                'name' => $v['name'],
                'nik' => $v['nik'] ?: '-',
                'pogiId' => $npa,
                'province' => $provName,
                'institution' => $instRow->name ?? ($v['institution'] ?: '-'),
                'phone' => $v['phone'] ?: '-',
                'qTarget' => 8.0,
                'qAchieved' => 0,
                'skpDisplay' => '0 / 160',
                'total5YearAchieved' => 0,
                'status' => 'Incomplete',
            ];
        } elseif ($entityType === 'institution') {
            $v = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'province' => ['required', 'string', 'max:120'],
                'address' => ['nullable', 'string', 'max:500'],
            ]);
            $provinceRow = DB::table('provinces')->where('name', 'like', '%' . $v['province'] . '%')->first()
                ?? DB::table('provinces')->first();
            $instId = (string) Str::uuid();
            DB::table('institutions')->insert([
                'id' => $instId,
                'name' => $v['name'],
                'province_id' => $provinceRow?->id,
                'address' => $v['address'] ?: ($v['province'] . ' - RS Jejaring'),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $item = [
                'id' => $instId,
                'name' => $v['name'],
                'doctorsCount' => 0,
                'province' => $provinceRow->name ?? $v['province'],
                'address' => $v['address'] ?: ($v['province'] . ' - RS Jejaring'),
                'type' => 'RS Jejaring',
                'status' => 'Aktif',
            ];
        } elseif ($entityType === 'event') {
            $v = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'type' => ['required', 'string', 'max:50'],
                'credits' => ['required', 'numeric', 'min:0.5'],
                'category' => ['nullable', 'string', 'max:120'],
                'date' => ['nullable', 'string', 'max:60'],
            ]);
            $domain = strtoupper($v['type']);
            $catRow = !empty($v['category'])
                ? DB::table('event_categories')->where('name', 'like', '%' . $v['category'] . '%')->first()
                : DB::table('event_categories')->where('domain', $domain)->first();
            if (!$catRow) {
                $catRow = DB::table('event_categories')->first();
            }
            $eventId = (string) Str::uuid();
            $eventDate = $now->toDateString();
            if (!empty($v['date'])) {
                try {
                    $eventDate = Carbon::parse($v['date'])->toDateString();
                } catch (\Throwable $e) {}
            }
            DB::table('skp_events')->insert([
                'id' => $eventId,
                'category_id' => $catRow?->id,
                'title' => $v['name'],
                'event_date' => $eventDate,
                'credit_value' => (float) $v['credits'],
                'organizer' => 'PP POGI',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $item = [
                'id' => $eventId,
                'name' => $v['name'],
                'title' => $v['name'],
                'category' => $catRow->name ?? ($v['category'] ?: 'Simposium Ilmiah'),
                'date' => $eventDate,
                'type' => ucfirst(strtolower($domain)),
                'credits' => (float) $v['credits'],
                'creditsDisplay' => (string) $v['credits'],
                'doctors' => '0 selesai',
                'doctorsCompleted' => 0,
                'status' => 'Dibuka',
            ];
        } elseif ($entityType === 'category') {
            $v = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'type' => ['required', 'string', 'max:50'],
            ]);
            $domain = strtoupper($v['type']);
            $catId = (string) Str::uuid();
            DB::table('event_categories')->insert([
                'id' => $catId,
                'name' => $v['name'],
                'domain' => $domain,
                'default_credit_value' => 2.0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $item = [
                'id' => $catId,
                'name' => $v['name'],
                'type' => ucfirst(strtolower($domain)),
                'standardSkp' => '2 SKP',
            ];
        } elseif ($entityType === 'user') {
            $v = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'role' => ['required', 'string', 'max:50'],
            ]);
            $roleEnumMap = [
                'Super Admin' => 'SUPER_ADMIN',
                'Admin Pusat' => 'ADMIN_PUSAT',
                'Admin POGI' => 'ADMIN_POGI',
                'Admin Cabang' => 'ADMIN_CABANG',
                'User/Dokter' => 'DOKTER',
            ];
            $dbRole = $roleEnumMap[$v['role']] ?? strtoupper(str_replace(' ', '_', $v['role']));
            $userId = (string) Str::uuid();
            User::create([
                'id' => $userId,
                'full_name' => $v['name'],
                'email' => strtolower(trim($v['email'])),
                'role' => $dbRole,
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]);
            $item = [
                'id' => $userId,
                'name' => $v['name'],
                'email' => strtolower(trim($v['email'])),
                'role' => $v['role'],
                'created' => $now->format('d M Y'),
            ];
        } else {
            return response()->json(['success' => false, 'message' => 'Tipe entitas tidak valid.'], 422);
        }

        DB::table('audit_logs')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => $actorId,
            'action' => 'CREATE_' . strtoupper($entityType),
            'entity_name' => $entityType,
            'entity_id' => $item['id'],
            'new_values' => json_encode(['message' => "Menambahkan master {$entityType}: {$item['name']}"]),
            'created_at' => $now,
        ]);

        return response()->json(['success' => true, 'item' => $item]);
    }
}
