<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Infrastructure\Security\NikCipher;
use App\Models\AuditLog;
use App\Models\Doctor;
use App\Models\DoctorSkpPeriod;
use App\Models\EventCategory;
use App\Models\Institution;
use App\Models\PogiBranch;
use App\Models\Province;
use App\Models\SkpEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EloquentManageRepository
{
    public const ROLE_DISPLAY_MAP = [
        'SUPER_ADMIN'  => 'Super Admin',
        'ADMIN_PUSAT'  => 'Admin Pusat',
        'ADMIN_POGI'   => 'Admin POGI',
        'ADMIN_CABANG' => 'Admin Cabang',
        'DOKTER'       => 'User/Dokter',
    ];

    public const ROLE_ENUM_MAP = [
        'Super Admin'  => 'SUPER_ADMIN',
        'Admin Pusat'  => 'ADMIN_PUSAT',
        'Admin POGI'   => 'ADMIN_POGI',
        'Admin Cabang' => 'ADMIN_CABANG',
        'User/Dokter'  => 'DOKTER',
    ];

    /**
     * Ambil seluruh data master untuk halaman Manage via Eloquent Models.
     */
    public function getAllMasterData(): array
    {
        $picCounts = Doctor::query()
            ->select('assigned_pic_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('assigned_pic_id')
            ->groupBy('assigned_pic_id')
            ->pluck('total', 'assigned_pic_id');

        $auditCounts = AuditLog::query()
            ->select('user_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $users = User::query()
            ->leftJoin('pogi_branches', 'users.branch_id', '=', 'pogi_branches.id')
            ->select(
                'users.id',
                'users.full_name',
                'users.full_name as name',
                'users.email',
                'users.phone_number',
                'users.role',
                'users.branch_id',
                'users.is_active',
                'users.account_status',
                'users.email_verified_at',
                'users.last_login_at',
                'users.created_at',
                'pogi_branches.branch_name'
            )
            ->orderBy('users.full_name')
            ->get()
            ->map(function ($u) use ($picCounts, $auditCounts) {
                $obj = (object) $u->toArray();
                $obj->raw_role = $u->role;
                $obj->display_role = self::ROLE_DISPLAY_MAP[$u->role] ?? $u->role;
                $obj->role = $obj->display_role;
                $obj->is_active = (bool) ($u->is_active ?? true);
                $obj->account_status = strtoupper((string) ($u->account_status ?: ($obj->is_active ? 'ACTIVE' : 'INACTIVE')));
                $obj->email_verified = !empty($u->email_verified_at);
                $obj->last_login = $u->last_login_at ? Carbon::parse($u->last_login_at)->format('d M Y, H.i') . ' WIB' : 'Belum tercatat';
                $obj->created = $u->created_at ? Carbon::parse($u->created_at)->format('d M Y') : '-';
                $obj->assigned_doctors_count = (int) ($picCounts[$u->id] ?? 0);
                $obj->audit_actions_count = (int) ($auditCounts[$u->id] ?? 0);
                return $obj;
            });

        $branches = PogiBranch::query()
            ->withCount('doctors as doctor_count')
            ->orderBy('branch_name')
            ->get();

        $doctors = Doctor::query()
            ->with(['province', 'branch', 'institution', 'activeSkpPeriod', 'assignedPic'])
            ->orderBy('full_name')
            ->get()
            ->map(function (Doctor $doc) {
                $period = $doc->activeSkpPeriod;
                $targetTotal = (float) ($period->target_total_credits ?? 160);
                $achievedTotal = (float) ($period->achieved_total_credits ?? 0);
                $achievedPel = (float) ($period->achieved_pelayanan_credits ?? 0);
                $achievedPeng = (float) ($period->achieved_pengabdian_credits ?? 0);
                $achievedPemb = (float) ($period->achieved_pembelajaran_credits ?? 0);
                $nameParts = explode(' ', trim($doc->full_name));
                $initials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));

                return (object) [
                    'id' => $doc->id,
                    'npa_pogi' => $doc->npa_pogi,
                    'pogiId' => $doc->npa_pogi,
                    'name' => $doc->full_name,
                    'specialization_title' => $doc->specialization_title,
                    'nik' => ($doc->nik_hash ? ('3171' . substr($doc->nik_hash, 0, 8) . '0001') : '3171xxxxxxxx0001'),
                    'str_number' => $doc->str_number,
                    'sip_number' => $doc->sip_number,
                    'sip_expiry_date' => $doc->sip_expiry_date,
                    'email' => $doc->email,
                    'phone' => $doc->whatsapp_number,
                    'plataran_sehat_username' => $doc->plataran_sehat_username,
                    'plataran_sehat_password' => $doc->plataran_sehat_pass_encrypted ?? '',
                    'period_start' => $period?->start_date ? Carbon::parse($period->start_date)->format('Y-m-d') : '2026-01-01',
                    'period_end' => $period?->end_date ? Carbon::parse($period->end_date)->format('Y-m-d') : '2030-12-31',
                    'is_active' => (bool) ($doc->is_active ?? true),
                    'status' => ($doc->is_active ?? true) ? 'Active' : 'Non-Active',
                    'raw_status' => ($doc->is_active ?? true) ? 'ACTIVE' : 'INACTIVE',
                    'compliance_status' => ($doc->status === 'COMPLETE' || $achievedTotal >= 8.0) ? 'Complete' : 'Incomplete',
                    'assigned_pic_id' => $doc->assigned_pic_id,
                    'assigned_to' => $doc->assignedPic?->full_name,
                    'assigned_pic_name' => $doc->assignedPic?->full_name,
                    'province_id' => $doc->province_id,
                    'province' => $doc->province?->name ?? '-',
                    'branch_id' => $doc->branch_id,
                    'branch' => $doc->branch?->branch_name,
                    'primary_institution_id' => $doc->primary_institution_id,
                    'institution' => $doc->institution?->name,
                    'hospital' => $doc->institution?->name,
                    'target_credits' => $targetTotal,
                    'total5YearAchieved' => $achievedTotal,
                    'qAchieved' => $achievedTotal,
                    'qTarget' => 8.0,
                    'skpDisplay' => "{$achievedTotal} / {$targetTotal}",
                    'pelayanan' => ['achieved' => $achievedPel, 'target' => 56],
                    'pengabdian' => ['achieved' => $achievedPeng, 'target' => 8],
                    'pembelajaran' => ['achieved' => $achievedPemb, 'target' => 72],
                    'fleksibilitas' => ['achieved' => 0, 'target' => 24],
                    'pelayananCredits' => $achievedPel,
                    'pengabdianCredits' => $achievedPeng,
                    'pembelajaranCredits' => $achievedPemb,
                    'pendingCredits' => 0,
                    'avatar' => $initials,
                    'initials' => $initials,
                    'lastActivity' => ($doc->updated_at ?? $doc->created_at) ? Carbon::parse($doc->updated_at ?? $doc->created_at)->format('d M Y, H.i') . ' WIB' : '-',
                ];
            });

        $provinces = Province::query()->orderBy('name')->get();

        $institutions = Institution::query()
            ->with('province')
            ->withCount('doctors as doctorsCount')
            ->orderBy('name')
            ->get()
            ->map(function (Institution $inst) {
                $lastTimestamp = $inst->updated_at ?? $inst->created_at;
                $formattedTime = $lastTimestamp ? Carbon::parse($lastTimestamp)->format('d M Y, H.i') . ' WIB' : '-';
                $isActive = $inst->is_active !== null ? (bool) $inst->is_active : ($inst->status !== 'INACTIVE' && $inst->status !== 'Nonaktif');
                $statusLabel = $isActive ? 'Aktif' : 'Nonaktif';
                return (object) [
                    'id' => $inst->id,
                    'province_id' => $inst->province_id,
                    'code' => $inst->code,
                    'name' => $inst->name,
                    'institution_type' => $inst->institution_type,
                    'hospital_class' => $inst->hospital_class,
                    'address' => $inst->address,
                    'city' => $inst->city,
                    'province_name' => $inst->province?->name,
                    'province' => $inst->province?->name,
                    'doctorsCount' => (int) ($inst->doctors_count ?? $inst->doctorsCount ?? 0),
                    'type' => $inst->institution_type ?: 'RS Jejaring',
                    'is_active' => $isActive,
                    'status' => $statusLabel,
                    'updated_at' => $inst->updated_at?->toISOString() ?? $inst->created_at?->toISOString(),
                    'lastActivity' => $formattedTime,
                ];
            });

        $defaultTypes = ['RSUD', 'RSUP', 'RS Swasta', 'RS Jejaring', 'Klinik Utama', 'Klinik Pratama', 'Puskesmas'];
        $dbTypes = Institution::query()
            ->whereNotNull('institution_type')
            ->where('institution_type', '!=', '')
            ->distinct()
            ->pluck('institution_type')
            ->toArray();
        $institutionTypes = array_values(array_unique(array_filter(array_merge($defaultTypes, $dbTypes))));
        sort($institutionTypes);

        $categories = EventCategory::query()
            ->orderBy('name')
            ->get()
            ->map(function (EventCategory $cat) {
                return (object) [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'domain' => $cat->domain,
                    'default_credit_value' => $cat->default_credit_value,
                    'description' => $cat->description,
                    'type' => $cat->domain ?? 'Pembelajaran',
                    'standardSkp' => ($cat->default_credit_value ?? 2) . ' SKP',
                    'status' => 'ACTIVE',
                ];
            });

        $eventCompletions = DB::table('skp_entries')
            ->select('event_id', DB::raw('COUNT(DISTINCT doctor_id) as done_count'))
            ->whereNotNull('event_id')
            ->groupBy('event_id')
            ->pluck('done_count', 'event_id');

        $events = SkpEvent::query()
            ->with('category')
            ->orderBy('event_date', 'desc')
            ->get()
            ->map(function (SkpEvent $e) use ($eventCompletions) {
                $doneCount = (int) ($eventCompletions[$e->id] ?? 0);
                return (object) [
                    'id' => $e->id,
                    'category_id' => $e->category_id,
                    'name' => $e->title,
                    'title' => $e->title,
                    'date' => $e->event_date,
                    'credits' => $e->credit_value,
                    'creditsDisplay' => (string) $e->credit_value,
                    'organizer' => $e->organizer,
                    'kemenkes_accreditation_number' => $e->kemenkes_accreditation_number,
                    'plataran_sehat_course_id' => $e->plataran_sehat_course_id,
                    'category_name' => $e->category?->name,
                    'category' => $e->category?->name,
                    'type' => $e->category?->domain ?? 'Pembelajaran',
                    'doctors' => "{$doneCount} selesai",
                    'doctorsCompleted' => $doneCount,
                    'status' => 'Dibuka',
                ];
            });

        $adminPogiList = User::where('role', 'ADMIN_POGI')
            ->where('is_active', true)
            ->orderBy('full_name')
            ->get();

        return compact('users', 'branches', 'doctors', 'provinces', 'institutions', 'categories', 'events', 'institutionTypes', 'adminPogiList');
    }

    public function createEntity(string $entityType, array $v, ?string $actorId): array
    {
        $now = Carbon::now();
        $item = null;

        if ($entityType === 'doctor') {
            $provinceRow = Province::where('id', $v['province'])->orWhere('name', 'like', '%' . $v['province'] . '%')->first()
                ?? Province::first();
            $branchRow = !empty($v['branch_id'])
                ? PogiBranch::find($v['branch_id'])
                : ($provinceRow ? PogiBranch::where('province_id', $provinceRow->id)->first() : null);
            $branchRow = $branchRow ?? PogiBranch::first();
            $instRow = !empty($v['institution_id'])
                ? Institution::find($v['institution_id'])
                : (!empty($v['institution']) ? Institution::where('name', 'like', '%' . $v['institution'] . '%')->first() : null);

            $npa = !empty($v['npa_pogi']) ? trim($v['npa_pogi']) : ('#POGI-' . $now->year . '-' . rand(2000, 9999));
            $provName = $provinceRow->name ?? $v['province'];
            $strNo = !empty($v['str_number']) ? trim($v['str_number']) : ('31.1.2.' . rand(100, 999) . '.1.26');
            $sipNo = !empty($v['sip_number']) ? trim($v['sip_number']) : ('446/' . rand(100, 999) . '/SIP/' . $now->year);
            $sipExpiry = !empty($v['sip_expiry_date']) ? Carbon::parse($v['sip_expiry_date'])->toDateString() : $now->copy()->addYears(5)->toDateString();
            $specTitle = !empty($v['specialization_title']) ? trim($v['specialization_title']) : 'Sp.OG';
            $docEmail = !empty($v['email']) ? strtolower(trim($v['email'])) : (Str::slug($v['name'], '.') . '@pogi-member.id');
            $docPhone = !empty($v['phone']) ? trim($v['phone']) : '+6281200000000';
            if (str_starts_with($docPhone, '0')) {
                $docPhone = '+62' . substr($docPhone, 1);
            } elseif (!str_starts_with($docPhone, '+')) {
                $docPhone = '+62' . $docPhone;
            }
            $rawStatus = !empty($v['status']) ? $v['status'] : 'ACTIVE';
            $isActive = array_key_exists('is_active', $v)
                ? (bool) $v['is_active']
                : !in_array(strtoupper(trim((string)$rawStatus)), ['INACTIVE', 'NON-ACTIVE', 'NONAKTIF', '0', 'FALSE'], true);
            $rawNik = (string) (!empty($v['nik']) ? $v['nik'] : $npa);

            $plataranUser = !empty($v['plataran_sehat_username']) ? trim($v['plataran_sehat_username']) : null;
            $plataranPass = !empty($v['plataran_sehat_password']) ? trim($v['plataran_sehat_password']) : null;

            $rawPic = !empty($v['assigned_pic_id']) ? $v['assigned_pic_id'] : (!empty($v['assigned_to']) ? $v['assigned_to'] : null);
            $assignedPicId = null;
            if ($rawPic) {
                $cleanPic = trim(str_ireplace(['(Admin POGI)', 'Admin POGI'], '', (string) $rawPic));
                $picUser = User::where('id', $rawPic)->first()
                    ?? User::where('full_name', 'like', '%' . $cleanPic . '%')
                    ->orWhere('email', 'like', '%' . $cleanPic . '%')
                    ->first();
                $assignedPicId = $picUser?->id;
            }

            $doc = Doctor::create([
                'id' => (string) Str::uuid(),
                'npa_pogi' => $npa,
                'nik_encrypted' => NikCipher::encrypt($rawNik),
                'nik_hash' => NikCipher::hash($rawNik),
                'str_number' => $strNo,
                'sip_number' => $sipNo,
                'sip_expiry_date' => $sipExpiry,
                'full_name' => $v['name'],
                'specialization_title' => $specTitle,
                'is_active' => $isActive,
                'email' => $docEmail,
                'whatsapp_number' => $docPhone,
                'province_id' => $provinceRow?->id,
                'branch_id' => $branchRow?->id,
                'primary_institution_id' => $instRow?->id,
                'assigned_pic_id' => $assignedPicId,
                'plataran_sehat_username' => $plataranUser,
                'plataran_sehat_pass_encrypted' => $plataranPass,
                'status' => 'INCOMPLETE',
            ]);

            $cleanDate = function($val) {
                if (!$val) return null;
                return str_ireplace(['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'], ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], (string) $val);
            };

            try {
                $pStart = !empty($v['period_start']) ? Carbon::parse($cleanDate($v['period_start']))->toDateString() : $now->copy()->startOfYear()->toDateString();
            } catch (\Throwable $e) {
                $pStart = $now->copy()->startOfYear()->toDateString();
            }
            try {
                $pEnd = !empty($v['period_end']) ? Carbon::parse($cleanDate($v['period_end']))->toDateString() : $now->copy()->addYears(5)->endOfYear()->toDateString();
            } catch (\Throwable $e) {
                $pEnd = $now->copy()->addYears(5)->endOfYear()->toDateString();
            }

            DoctorSkpPeriod::create([
                'id' => (string) Str::uuid(),
                'doctor_id' => $doc->id,
                'cycle_number' => 1,
                'start_date' => $pStart,
                'end_date' => $pEnd,
                'target_total_credits' => 160.00,
                'target_pembelajaran_credits' => 72.00,
                'target_pelayanan_credits' => 56.00,
                'target_pengabdian_credits' => 8.00,
                'target_flexibility_credits' => 24.00,
                'achieved_total_credits' => 0,
                'achieved_pelayanan_credits' => 0,
                'achieved_pengabdian_credits' => 0,
                'achieved_pembelajaran_credits' => 0,
                'is_active' => true,
                'is_completed' => false,
            ]);

            $item = [
                'id' => $doc->id,
                'name' => $v['name'],
                'specialization_title' => $specTitle,
                'npa_pogi' => $npa,
                'nik' => !empty($v['nik']) ? (strlen($v['nik']) >= 8 ? substr($v['nik'], 0, 4) . 'xxxxxxxx' . substr($v['nik'], -4) : $v['nik']) : '-',
                'pogiId' => $npa,
                'str_number' => $strNo,
                'sip_number' => $sipNo,
                'sip_expiry_date' => $sipExpiry,
                'email' => $docEmail,
                'province_id' => $provinceRow?->id,
                'province' => $provName,
                'branch_id' => $branchRow?->id,
                'branch' => $branchRow?->branch_name,
                'primary_institution_id' => $instRow?->id,
                'institution' => $instRow->name ?? (!empty($v['institution']) ? $v['institution'] : '-'),
                'hospital' => $instRow->name ?? (!empty($v['institution']) ? $v['institution'] : '-'),
                'phone' => $docPhone,
                'plataran_sehat_username' => $plataranUser,
                'period_start' => $pStart,
                'period_end' => $pEnd,
                'qTarget' => 8.0,
                'qAchieved' => 0,
                'skpDisplay' => '0 / 160',
                'total5YearAchieved' => 0,
                'is_active' => $isActive,
                'status' => $isActive ? 'Active' : 'Non-Active',
                'raw_status' => $isActive ? 'ACTIVE' : 'INACTIVE',
                'compliance_status' => 'Incomplete',
            ];
        } elseif ($entityType === 'institution') {
            $provinceRow = Province::where('id', $v['province'])->orWhere('name', 'like', '%' . $v['province'] . '%')->first()
                ?? Province::first();
            $instType = !empty($v['institution_type']) ? $v['institution_type'] : 'RS Jejaring';
            $instAddr = !empty($v['address']) ? $v['address'] : (($provinceRow->name ?? $v['province']) . ' - ' . $instType);

            $rawSt = !empty($v['status']) ? strtoupper(trim($v['status'])) : 'ACTIVE';
            $isActive = array_key_exists('is_active', $v) ? (bool) $v['is_active'] : ($rawSt === 'ACTIVE' || $rawSt === 'AKTIF');
            $stVal = $isActive ? 'ACTIVE' : 'INACTIVE';

            $inst = Institution::create([
                'id' => (string) Str::uuid(),
                'code' => !empty($v['code']) ? trim($v['code']) : null,
                'name' => $v['name'],
                'province_id' => $provinceRow?->id,
                'institution_type' => $instType,
                'hospital_class' => !empty($v['hospital_class']) ? $v['hospital_class'] : null,
                'city' => !empty($v['city']) ? trim($v['city']) : null,
                'address' => $instAddr,
                'is_active' => $isActive,
                'status' => $stVal,
            ]);

            $item = [
                'id' => $inst->id,
                'code' => $inst->code,
                'name' => $inst->name,
                'doctorsCount' => 0,
                'province_id' => $provinceRow?->id,
                'province' => $provinceRow->name ?? $v['province'],
                'city' => $inst->city,
                'address' => $instAddr,
                'institution_type' => $instType,
                'hospital_class' => $inst->hospital_class,
                'type' => $instType,
                'is_active' => $isActive,
                'status' => $isActive ? 'Aktif' : 'Nonaktif',
                'updated_at' => $inst->updated_at?->toISOString() ?? $now->toISOString(),
                'lastActivity' => $now->format('d M Y, H.i') . ' WIB',
            ];
        } elseif ($entityType === 'event') {
            $domain = strtoupper((string) ($v['type'] ?? 'PEMBELAJARAN'));
            $catRow = !empty($v['category_id'])
                ? EventCategory::find($v['category_id'])
                : (!empty($v['category'])
                    ? EventCategory::where('name', 'like', '%' . $v['category'] . '%')->first()
                    : EventCategory::where('domain', $domain)->first());
            $catRow = $catRow ?? EventCategory::first();

            $eventDate = $now->toDateString();
            if (!empty($v['date'])) {
                try { $eventDate = Carbon::parse($v['date'])->toDateString(); } catch (\Throwable $e) {}
            }
            $organizer = !empty($v['organizer']) ? trim($v['organizer']) : 'PP POGI';

            $ev = SkpEvent::create([
                'id' => (string) Str::uuid(),
                'category_id' => $catRow?->id,
                'title' => $v['name'],
                'event_date' => $eventDate,
                'credit_value' => (float) $v['credits'],
                'organizer' => $organizer,
                'kemenkes_accreditation_number' => !empty($v['kemenkes_accreditation_number']) ? trim($v['kemenkes_accreditation_number']) : null,
                'plataran_sehat_course_id' => !empty($v['plataran_sehat_course_id']) ? trim($v['plataran_sehat_course_id']) : null,
            ]);

            $resolvedDomain = $catRow->domain ?? $domain;
            $item = [
                'id' => $ev->id,
                'category_id' => $catRow?->id,
                'name' => $ev->title,
                'title' => $ev->title,
                'organizer' => $organizer,
                'category' => $catRow->name ?? (!empty($v['category']) ? $v['category'] : 'Simposium Ilmiah'),
                'date' => $eventDate,
                'type' => ucfirst(strtolower($resolvedDomain)),
                'credits' => (float) $v['credits'],
                'creditsDisplay' => (string) $v['credits'],
                'kemenkes_accreditation_number' => $ev->kemenkes_accreditation_number,
                'plataran_sehat_course_id' => $ev->plataran_sehat_course_id,
                'doctors' => '0 selesai',
                'doctorsCompleted' => 0,
                'status' => 'Dibuka',
            ];
        } elseif ($entityType === 'category') {
            $domain = strtoupper($v['type']);
            $defCred = isset($v['default_credit_value']) && $v['default_credit_value'] !== '' ? (float) $v['default_credit_value'] : 2.0;

            $cat = EventCategory::create([
                'id' => (string) Str::uuid(),
                'name' => $v['name'],
                'domain' => $domain,
                'default_credit_value' => $defCred,
                'description' => !empty($v['description']) ? trim($v['description']) : null,
            ]);

            $item = [
                'id' => $cat->id,
                'name' => $cat->name,
                'domain' => $domain,
                'type' => ucfirst(strtolower($domain)),
                'default_credit_value' => $defCred,
                'standardSkp' => $defCred . ' SKP',
                'description' => $cat->description,
                'status' => $v['status'] ?? 'ACTIVE',
            ];
        } elseif ($entityType === 'user') {
            $dbRole = self::ROLE_ENUM_MAP[$v['role']] ?? strtoupper(str_replace(' ', '_', $v['role']));
            $accStatus = strtoupper((string) ($v['account_status'] ?? 'ACTIVE'));
            $branchRow = !empty($v['branch_id']) ? PogiBranch::find($v['branch_id']) : null;

            $usr = User::create([
                'id' => (string) Str::uuid(),
                'full_name' => $v['name'],
                'email' => strtolower(trim($v['email'])),
                'phone_number' => !empty($v['phone_number']) ? trim($v['phone_number']) : null,
                'role' => $dbRole,
                'branch_id' => $branchRow?->id,
                'password_hash' => Hash::make($v['password']),
                'is_active' => $accStatus === 'ACTIVE',
                'account_status' => $accStatus,
            ]);

            $item = [
                'id' => $usr->id,
                'name' => $usr->full_name,
                'email' => $usr->email,
                'phone_number' => $usr->phone_number,
                'role' => $v['role'],
                'raw_role' => $dbRole,
                'branch_id' => $branchRow?->id,
                'branch_name' => $branchRow?->branch_name,
                'is_active' => $accStatus === 'ACTIVE',
                'account_status' => $accStatus,
                'created' => $now->format('d M Y'),
            ];
        }

        $this->recordAudit($actorId, 'CREATE_' . strtoupper($entityType), $entityType, $item['id'], "Menambahkan master {$entityType}: {$item['name']}");
        return $item;
    }

    public function updateUserStatus(string $id, array $v, ?string $actorId): ?string
    {
        $target = User::find($id);
        if (!$target) {
            return null;
        }

        $accStatus = strtoupper($v['account_status']);
        $updateData = [
            'account_status' => $accStatus,
            'is_active' => $accStatus === 'ACTIVE',
        ];
        if (!empty($v['name'])) $updateData['full_name'] = $v['name'];
        if (!empty($v['email'])) $updateData['email'] = strtolower(trim($v['email']));
        if (!empty($v['role'])) {
            $updateData['role'] = self::ROLE_ENUM_MAP[$v['role']] ?? strtoupper(str_replace(' ', '_', $v['role']));
        }

        User::where('id', $id)->update($updateData);
        $this->recordAudit($actorId, 'UPDATE_USER_STATUS', 'users', $id, "Status akun {$target->full_name} diubah ke {$accStatus}");

        return $accStatus;
    }

    public function updateEntity(string $type, string $id, array $v, ?string $actorId): void
    {
        $label = $id;

        if ($type === 'doctor') {
            $prov = Province::where('id', $v['province'])->orWhere('name', 'like', '%' . $v['province'] . '%')->first();
            $branch = !empty($v['branch_id'])
                ? PogiBranch::find($v['branch_id'])
                : ($prov ? PogiBranch::where('province_id', $prov->id)->first() : null);
            $inst = !empty($v['institution_id'])
                ? Institution::find($v['institution_id'])
                : (!empty($v['institution']) ? Institution::where('name', 'like', '%' . $v['institution'] . '%')->first() : null);

            $upd = ['full_name' => $v['name']];
            if (!empty($v['specialization_title'])) $upd['specialization_title'] = trim($v['specialization_title']);
            if (!empty($v['npa_pogi'])) $upd['npa_pogi'] = trim($v['npa_pogi']);
            if (!empty($v['str_number'])) $upd['str_number'] = trim($v['str_number']);
            if (array_key_exists('sip_number', $v)) $upd['sip_number'] = !empty($v['sip_number']) ? trim($v['sip_number']) : null;
            if (!empty($v['sip_expiry_date'])) $upd['sip_expiry_date'] = Carbon::parse($v['sip_expiry_date'])->toDateString();
            if (array_key_exists('email', $v)) $upd['email'] = !empty($v['email']) ? strtolower(trim($v['email'])) : null;
            if (!empty($v['phone'])) {
                $ph = trim($v['phone']);
                if (str_starts_with($ph, '0')) {
                    $ph = '+62' . substr($ph, 1);
                } elseif (!str_starts_with($ph, '+')) {
                    $ph = '+62' . $ph;
                }
                $upd['whatsapp_number'] = $ph;
            }
            if (array_key_exists('plataran_sehat_username', $v)) {
                $upd['plataran_sehat_username'] = !empty($v['plataran_sehat_username']) ? trim($v['plataran_sehat_username']) : null;
            }
            if (array_key_exists('plataran_sehat_password', $v)) {
                $upd['plataran_sehat_pass_encrypted'] = !empty($v['plataran_sehat_password']) ? trim($v['plataran_sehat_password']) : null;
            }
            if (array_key_exists('is_active', $v)) {
                $upd['is_active'] = (bool) $v['is_active'];
            } elseif (!empty($v['status'])) {
                $st = strtoupper(trim($v['status']));
                if (in_array($st, ['ACTIVE', 'AKTIF'], true)) {
                    $upd['is_active'] = true;
                } elseif (in_array($st, ['INACTIVE', 'NON-ACTIVE', 'NONAKTIF'], true)) {
                    $upd['is_active'] = false;
                } elseif (in_array($st, ['COMPLETE', 'INCOMPLETE', 'PENDING_REVIEW'], true)) {
                    $upd['status'] = $st;
                }
            }
            $hasPicKey = array_key_exists('assigned_pic_id', $v) || array_key_exists('assigned_to', $v);
            if ($hasPicKey) {
                $rawPic = !empty($v['assigned_pic_id']) ? $v['assigned_pic_id'] : (!empty($v['assigned_to']) ? $v['assigned_to'] : null);
                if (!empty($rawPic)) {
                    $cleanPic = trim(str_ireplace(['(Admin POGI)', 'Admin POGI'], '', (string) $rawPic));
                    $picUser = User::where('id', $rawPic)->first()
                        ?? User::where('full_name', 'like', '%' . $cleanPic . '%')
                        ->orWhere('email', 'like', '%' . $cleanPic . '%')
                        ->first();
                    $upd['assigned_pic_id'] = $picUser?->id;
                } else {
                    $upd['assigned_pic_id'] = null;
                }
            }
            if ($prov) $upd['province_id'] = $prov->id;
            if ($branch) $upd['branch_id'] = $branch->id;
            if ($inst) $upd['primary_institution_id'] = $inst->id;
            if (!empty($v['nik']) && !str_contains($v['nik'], 'x') && !str_starts_with($v['nik'], 'GCM1:')) {
                $upd['nik_encrypted'] = NikCipher::encrypt($v['nik']);
                $upd['nik_hash'] = NikCipher::hash($v['nik']);
            }
            $docRecord = Doctor::find($id);
            if ($docRecord) {
                $docRecord->update($upd);
            } else {
                Doctor::where('id', $id)->update($upd);
            }

            $cleanDate = function($val) {
                if (!$val) return null;
                return str_ireplace(['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'], ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], (string) $val);
            };

            if (!empty($v['period_start']) || !empty($v['period_end'])) {
                $pUpd = [];
                if (!empty($v['period_start'])) {
                    try { $pUpd['start_date'] = Carbon::parse($cleanDate($v['period_start']))->toDateString(); } catch (\Throwable $e) {}
                }
                if (!empty($v['period_end'])) {
                    try { $pUpd['end_date'] = Carbon::parse($cleanDate($v['period_end']))->toDateString(); } catch (\Throwable $e) {}
                }
                if (!empty($pUpd)) {
                    DoctorSkpPeriod::where('doctor_id', $id)->where('is_active', true)->update($pUpd);
                }
            }
            $label = $v['name'];
        } elseif ($type === 'institution') {
            $prov = Province::where('id', $v['province'])->orWhere('name', 'like', '%' . $v['province'] . '%')->first();
            $rawSt = !empty($v['status']) ? strtoupper(trim($v['status'])) : null;
            $isActive = array_key_exists('is_active', $v)
                ? (bool) $v['is_active']
                : ($rawSt !== null ? ($rawSt === 'ACTIVE' || $rawSt === 'AKTIF') : true);
            $upd = [
                'name' => $v['name'],
                'code' => !empty($v['code']) ? trim($v['code']) : null,
                'institution_type' => !empty($v['institution_type']) ? $v['institution_type'] : 'RS Jejaring',
                'hospital_class' => !empty($v['hospital_class']) ? $v['hospital_class'] : null,
                'city' => !empty($v['city']) ? trim($v['city']) : null,
                'address' => !empty($v['address']) ? $v['address'] : '',
                'is_active' => $isActive,
                'status' => $isActive ? 'ACTIVE' : 'INACTIVE',
            ];
            if ($prov) $upd['province_id'] = $prov->id;
            Institution::where('id', $id)->update($upd);
            $label = $v['name'];
        } elseif ($type === 'event') {
            $domain = strtoupper((string) ($v['type'] ?? 'PEMBELAJARAN'));
            $catRow = !empty($v['category_id'])
                ? EventCategory::find($v['category_id'])
                : (!empty($v['category'])
                    ? EventCategory::where('name', 'like', '%' . $v['category'] . '%')->first()
                    : EventCategory::where('domain', $domain)->first());
            $upd = [
                'title' => $v['name'],
                'credit_value' => (float) $v['credits'],
                'organizer' => !empty($v['organizer']) ? trim($v['organizer']) : 'PP POGI',
                'kemenkes_accreditation_number' => !empty($v['kemenkes_accreditation_number']) ? trim($v['kemenkes_accreditation_number']) : null,
                'plataran_sehat_course_id' => !empty($v['plataran_sehat_course_id']) ? trim($v['plataran_sehat_course_id']) : null,
            ];
            if ($catRow) $upd['category_id'] = $catRow->id;
            if (!empty($v['date'])) {
                try { $upd['event_date'] = Carbon::parse($v['date'])->toDateString(); } catch (\Throwable $e) {}
            }
            SkpEvent::where('id', $id)->update($upd);
            $label = $v['name'];
        } elseif ($type === 'category') {
            $upd = [
                'name' => $v['name'],
                'domain' => strtoupper($v['type']),
                'description' => !empty($v['description']) ? trim($v['description']) : null,
            ];
            if (isset($v['default_credit_value']) && $v['default_credit_value'] !== '') {
                $upd['default_credit_value'] = (float) $v['default_credit_value'];
            }
            EventCategory::where('id', $id)->update($upd);
            $label = $v['name'];
        } elseif ($type === 'user') {
            $dbRole = self::ROLE_ENUM_MAP[$v['role']] ?? strtoupper(str_replace(' ', '_', $v['role']));
            $accStatus = strtoupper($v['account_status']);
            $upd = [
                'full_name' => $v['name'],
                'email' => strtolower(trim($v['email'])),
                'phone_number' => !empty($v['phone_number']) ? trim($v['phone_number']) : null,
                'role' => $dbRole,
                'branch_id' => !empty($v['branch_id']) ? $v['branch_id'] : null,
                'account_status' => $accStatus,
                'is_active' => $accStatus === 'ACTIVE',
            ];
            if (!empty($v['password'])) {
                $upd['password_hash'] = Hash::make($v['password']);
            }
            User::where('id', $id)->update($upd);
            $label = $v['name'];
        }

        $this->recordAudit($actorId, 'UPDATE_' . strtoupper($type), $type, $id, "Memperbarui master {$type}: {$label}");
    }

    public function deleteEntity(string $type, string $id, ?string $actorId): void
    {
        if ($type === 'doctor') {
            Doctor::where('id', $id)->delete();
        } elseif ($type === 'institution') {
            Doctor::where('primary_institution_id', $id)->update(['primary_institution_id' => null]);
            Institution::where('id', $id)->delete();
        } elseif ($type === 'event') {
            DB::table('skp_entries')->where('event_id', $id)->update(['event_id' => null]);
            SkpEvent::where('id', $id)->delete();
        } elseif ($type === 'category') {
            SkpEvent::where('category_id', $id)->update(['category_id' => null]);
            DB::table('skp_entries')->where('category_id', $id)->update(['category_id' => null]);
            EventCategory::where('id', $id)->delete();
        } elseif ($type === 'user') {
            Doctor::where('assigned_pic_id', $id)->update(['assigned_pic_id' => null]);
            User::where('id', $id)->forceDelete();
        }

        $this->recordAudit($actorId, 'DELETE_' . strtoupper($type), $type, $id, "Menghapus master {$type} ID: {$id}");
    }

    private function recordAudit(?string $actorId, string $action, string $entityName, string $entityId, string $message): void
    {
        AuditLog::create([
            'id' => (string) Str::uuid(),
            'user_id' => $actorId,
            'action' => $action,
            'entity_name' => $entityName,
            'entity_id' => $entityId,
            'new_values' => json_encode(['message' => $message]),
        ]);
    }
}
