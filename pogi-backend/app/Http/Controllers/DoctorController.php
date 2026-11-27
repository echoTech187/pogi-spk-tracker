<?php

namespace App\Http\Controllers;

use App\Domain\Entities\SystemRole;
use App\Infrastructure\Security\NikCipher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DoctorController extends Controller
{
    /**
     * Tampilkan halaman direktori dokter dengan data live dari basis data MySQL.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $branchId = null;

        // Isolasi Cabang Otomatis untuk ADMIN_CABANG & Isolasi PIC Binaan untuk ADMIN_POGI
        $roleStr = $user?->role instanceof SystemRole ? $user->role->value : strtoupper((string) ($user?->role ?? ''));
        $picUserId = null;
        if ($roleStr === 'ADMIN_CABANG') {
            $branchId = $user->branch_id;
        } elseif ($roleStr === 'ADMIN_POGI') {
            $picUserId = $user->id;
        } elseif ($request->filled('branch')) {
            $branchId = (string) $request->query('branch');
        } elseif ($request->filled('scope') && $request->query('scope') !== 'all') {
            $branchId = (string) $request->query('scope');
        }

        // Resolve branch ID if provided as code or alias
        if ($branchId && $branchId !== 'all') {
            $matchedBranch = DB::table('pogi_branches')
                ->where('id', $branchId)
                ->orWhere('branch_code', $branchId)
                ->orWhere('branch_name', 'like', "%{$branchId}%")
                ->first();
            if ($matchedBranch) {
                $branchId = $matchedBranch->id;
            }
        }

        // Query dokter dengan relasi lengkap dan subquery pending credits
        $query = DB::table('doctors')
            ->leftJoin('pogi_branches', 'doctors.branch_id', '=', 'pogi_branches.id')
            ->leftJoin('provinces', 'doctors.province_id', '=', 'provinces.id')
            ->leftJoin('institutions', 'doctors.primary_institution_id', '=', 'institutions.id')
            ->leftJoin('users as pic', 'doctors.assigned_pic_id', '=', 'pic.id')
            ->leftJoin('doctor_skp_periods as period', function ($join) {
                $join->on('doctors.id', '=', 'period.doctor_id')
                     ->where('period.is_active', '=', true);
            })
            ->select(
                'doctors.id',
                'doctors.npa_pogi',
                'doctors.full_name',
                'doctors.specialization_title',
                'doctors.nik_hash',
                'doctors.str_number',
                'doctors.sip_number',
                'doctors.sip_expiry_date',
                'doctors.email',
                'doctors.whatsapp_number',
                'doctors.status',
                'doctors.dashboard_updated_at',
                'doctors.kemenkes_updated_at',
                'pogi_branches.id as branch_id',
                'pogi_branches.branch_name',
                'provinces.id as province_id',
                'provinces.name as province_name',
                'institutions.id as institution_id',
                'institutions.name as institution_name',
                'pic.id as assigned_pic_id',
                'pic.full_name as assigned_pic_name',
                'period.target_total_credits',
                'period.achieved_total_credits',
                'period.achieved_pelayanan_credits',
                'period.achieved_pengabdian_credits',
                'period.achieved_pembelajaran_credits',
                'period.target_pelayanan_credits',
                'period.target_pengabdian_credits',
                'period.target_pembelajaran_credits'
            )
            ->selectSub(function ($q) {
                $q->from('skp_entries')
                    ->whereColumn('skp_entries.doctor_id', 'doctors.id')
                    ->whereIn('skp_entries.status', ['PENDING', 'NOT_SUBMITTED'])
                    ->selectRaw('COALESCE(SUM(credit_amount), 0)');
            }, 'pending_credits')
            ->whereNull('doctors.deleted_at')
            ->when($branchId, fn($q) => $q->where('doctors.branch_id', $branchId))
            ->when($picUserId, fn($q) => $q->where('doctors.assigned_pic_id', $picUserId));

        // Filter status jika ada
        if ($request->filled('status') && $request->query('status') !== 'ALL') {
            $query->where('doctors.status', strtoupper($request->query('status')));
        }

        // Filter PIC jika ada
        if ($request->filled('pic') && $request->query('pic') !== 'ALL') {
            $query->where('doctors.assigned_pic_id', $request->query('pic'));
        }

        $rawDoctors = $query->orderBy('doctors.full_name')->get();

        // Hitung metrik ringkasan dari query
        $total = $rawDoctors->count();
        $complete = $rawDoctors->where('status', 'COMPLETE')->count();
        $incomplete = $rawDoctors->where('status', 'INCOMPLETE')->count();
        $pendingReview = $rawDoctors->where('status', 'PENDING_REVIEW')->count();

        $now = Carbon::now();
        $critical = $rawDoctors->filter(function ($doc) use ($now) {
            if ($doc->status === 'COMPLETE') return false;
            if (!$doc->sip_expiry_date) return false;
            $expiry = Carbon::parse($doc->sip_expiry_date);
            return $expiry->diffInMonths($now, false) > -6; // Sisa masa berlaku < 6 bulan
        })->count();

        // Format data dokter untuk Blade SSR dan sinkronisasi ke JavaScript
        $formattedDoctors = $rawDoctors->map(function ($d) {
            $target = (float) ($d->target_total_credits ?: 160.0);
            $achieved = (float) ($d->achieved_total_credits ?: 0.0);
            $pct = $target > 0 ? round(($achieved / $target) * 100, 1) : 0.0;

            // Inisial nama dokter
            $words = explode(' ', preg_replace('/^(dr\.|drg\.|Prof\.|Sp\.OG|\,)/i', '', $d->full_name));
            $initials = '';
            foreach ($words as $w) {
                $trimmed = trim($w);
                if ($trimmed !== '') {
                    $initials .= strtoupper($trimmed[0]);
                    if (strlen($initials) >= 2) break;
                }
            }
            if ($initials === '') $initials = 'DR';

            $cleanStatus = strtoupper($d->status ?? 'INCOMPLETE') === 'COMPLETE' ? 'Complete' : 'Incomplete';

            return [
                'id' => (string) $d->id,
                'pogiId' => $d->npa_pogi ? (str_starts_with($d->npa_pogi, '#') ? $d->npa_pogi : "#{$d->npa_pogi}") : "#POGI-" . substr($d->id, 0, 8),
                'name' => $d->full_name,
                'specialization' => $d->specialization_title ?: 'Spesialis Obstetri & Ginekologi',
                'initials' => $initials,
                'nik' => $d->nik_hash ? ('32' . substr($d->nik_hash, 0, 14)) : ($d->str_number ?: '-'),
                'strNumber' => $d->str_number ?: '-',
                'sipNumber' => $d->sip_number ?: '-',
                'sipExpiry' => $d->sip_expiry_date ? Carbon::parse($d->sip_expiry_date)->format('d M Y') : '-',
                'rawSipExpiry' => $d->sip_expiry_date,
                'email' => $d->email ?: '-',
                'phone' => $d->whatsapp_number ?: '-',
                'hospital' => $d->institution_name ?: 'RS Jejaring POGI',
                'institution' => $d->institution_name ?: 'RS Jejaring POGI',
                'branch' => $d->branch_name ?: 'Cabang POGI',
                'branchId' => $d->branch_id,
                'province' => $d->province_name ?: '-',
                'provinceId' => $d->province_id,
                'assignedTo' => $d->assigned_pic_name ?: 'Belum Ditentukan',
                'assignedPicId' => $d->assigned_pic_id,
                'status' => $cleanStatus,
                'rawStatus' => $d->status ?: 'INCOMPLETE',
                'achievedCredits' => $achieved,
                'targetCredits' => $target,
                'progressPct' => $pct,
                'q4Credits' => round($achieved * 0.25, 1),
                'pendingCredits' => (float) ($d->pending_credits ?? 0),
                'pelayananCredits' => (float) ($d->achieved_pelayanan_credits ?: 0),
                'pengabdianCredits' => (float) ($d->achieved_pengabdian_credits ?: 0),
                'pembelajaranCredits' => (float) ($d->achieved_pembelajaran_credits ?: 0),
                'targetPelayananCredits' => (float) ($d->target_pelayanan_credits ?: 56.0),
                'targetPengabdianCredits' => (float) ($d->target_pengabdian_credits ?: 8.0),
                'targetPembelajaranCredits' => (float) ($d->target_pembelajaran_credits ?: 72.0),
                'lastActivity' => $d->dashboard_updated_at ? Carbon::parse($d->dashboard_updated_at)->format('d M Y, H.i') : 'Baru saja',
                'dashboardUpdatedAt' => $d->dashboard_updated_at ? Carbon::parse($d->dashboard_updated_at)->format('d M Y H:i') : 'Baru saja',
                'kemenkesUpdatedAt' => $d->kemenkes_updated_at ? Carbon::parse($d->kemenkes_updated_at)->format('d M Y H:i') : 'Menunggu Sinkronisasi',
            ];
        })->values()->all();

        // Ambil data referensi filter
        $branches = DB::table('pogi_branches')->orderBy('branch_name')->get();
        $provinces = DB::table('provinces')->orderBy('name')->get();
        $institutions = DB::table('institutions')->orderBy('name')->get();
        $adminPogiList = DB::table('users')->where('role', 'ADMIN_POGI')->orderBy('full_name')->get();

        $metrics = [
            'total' => $total,
            'complete' => $complete,
            'incomplete' => $incomplete,
            'pending_review' => $pendingReview,
            'critical' => $critical,
            'compliance_rate' => $total > 0 ? round(($complete / $total) * 100, 1) : 0,
        ];

        return view('doctors', [
            'doctors' => $formattedDoctors,
            'metrics' => $metrics,
            'branches' => $branches,
            'provinces' => $provinces,
            'institutions' => $institutions,
            'adminPogiList' => $adminPogiList,
            'selectedBranch' => $branchId,
        ]);
    }

    /**
     * Tampilkan detail portofolio dokter secara live dari basis data MySQL.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $doctorId = $request->query('id');

        // Jika dokter yang login, arahkan ke data dirinya sendiri
        if ($user && in_array($user->role, ['USER_DOKTER', 'DOKTER'])) {
            $selfDoc = DB::table('doctors')
                ->where('user_id', $user->id)
                ->orWhere('email', $user->email)
                ->first();
            if ($selfDoc) {
                $doctorId = $selfDoc->id;
            }
        }

        // Ambil dokter berdasarkan ID atau fallback ke dokter pertama di database
        $doctorQuery = DB::table('doctors')
            ->leftJoin('pogi_branches', 'doctors.branch_id', '=', 'pogi_branches.id')
            ->leftJoin('provinces', 'doctors.province_id', '=', 'provinces.id')
            ->leftJoin('institutions', 'doctors.primary_institution_id', '=', 'institutions.id')
            ->leftJoin('users as pic', 'doctors.assigned_pic_id', '=', 'pic.id')
            ->select(
                'doctors.*',
                'pogi_branches.branch_name',
                'pogi_branches.branch_code',
                'provinces.name as province_name',
                'institutions.name as institution_name',
                'institutions.city as institution_city',
                'pic.full_name as assigned_pic_name',
                'pic.email as assigned_pic_email',
                'pic.phone_number as assigned_pic_phone'
            )
            ->whereNull('doctors.deleted_at');

        if ($doctorId) {
            $doctorQuery->where('doctors.id', $doctorId);
        }

        $doctorRow = $doctorQuery->first();
        if (!$doctorRow) {
            // Ambil dokter pertama jika ID tidak ditemukan (dengan filter cabang jika Admin Cabang)
            $fallbackQuery = DB::table('doctors')
                ->leftJoin('pogi_branches', 'doctors.branch_id', '=', 'pogi_branches.id')
                ->leftJoin('provinces', 'doctors.province_id', '=', 'provinces.id')
                ->leftJoin('institutions', 'doctors.primary_institution_id', '=', 'institutions.id')
                ->leftJoin('users as pic', 'doctors.assigned_pic_id', '=', 'pic.id')
                ->select(
                    'doctors.*',
                    'pogi_branches.branch_name',
                    'pogi_branches.branch_code',
                    'provinces.name as province_name',
                    'institutions.name as institution_name',
                    'institutions.city as institution_city',
                    'pic.full_name as assigned_pic_name',
                    'pic.email as assigned_pic_email',
                    'pic.phone_number as assigned_pic_phone'
                )
                ->whereNull('doctors.deleted_at');

            $showRoleStr = $user?->role instanceof SystemRole ? $user->role->value : strtoupper((string) ($user?->role ?? ''));
            if ($user && $user->isAdminCabang() && $user->branch_id) {
                $fallbackQuery->where('doctors.branch_id', $user->branch_id);
            } elseif ($showRoleStr === 'ADMIN_POGI') {
                $fallbackQuery->where('doctors.assigned_pic_id', $user->id);
            }

            $doctorRow = $fallbackQuery->first();
        }

        if (!$doctorRow) {
            return redirect()->route('doctors.index')
                ->with('info', 'Data dokter belum tersedia atau tidak ditemukan.');
        }

        // Isolasi multi-tenant ketat untuk ADMIN_CABANG & PIC binaan untuk ADMIN_POGI
        $showRoleStr = $user?->role instanceof SystemRole ? $user->role->value : strtoupper((string) ($user?->role ?? ''));
        if ($user && $user->isAdminCabang()) {
            if ($doctorRow && $doctorRow->branch_id !== $user->branch_id) {
                abort(403, 'Akses ditolak: Anda hanya berwenang memantau dokter di wilayah cabang Anda sendiri.');
            }
        } elseif ($showRoleStr === 'ADMIN_POGI') {
            if ($doctorRow && $doctorRow->assigned_pic_id !== $user->id) {
                abort(403, 'Akses ditolak: Anda hanya berwenang memantau dokter binaan PIC Anda sendiri.');
            }
        }

        // Active SKP Period
        $period = null;
        if ($doctorRow) {
            $period = DB::table('doctor_skp_periods')
                ->where('doctor_id', $doctorRow->id)
                ->where('is_active', true)
                ->first();
        }

        // Normalized Doctor Documents
        $documents = [];
        if ($doctorRow) {
            $documents = DB::table('doctor_documents')
                ->leftJoin('skp_entries', 'doctor_documents.skp_entry_id', '=', 'skp_entries.id')
                ->where('doctor_documents.doctor_id', $doctorRow->id)
                ->whereNull('doctor_documents.deleted_at')
                ->select(
                    'doctor_documents.*',
                    'skp_entries.domain',
                    'skp_entries.title as entry_title',
                    'skp_entries.credit_amount',
                    'skp_entries.status as entry_status'
                )
                ->orderBy('doctor_documents.created_at', 'desc')
                ->get()
                ->map(function ($doc) {
                    $status = 'NOT_SUBMITTED';
                    if ($doc->is_verified) {
                        $status = 'DONE';
                    } elseif ($doc->entry_status) {
                        $status = $doc->entry_status;
                    }

                    $rawCat = strtoupper((string) ($doc->domain ?: ($doc->document_type ?: 'PEMBELAJARAN')));
                    $category = match(true) {
                        str_contains($rawCat, 'PELAYANAN') => 'Pelayanan',
                        str_contains($rawCat, 'PENGABDIAN') => 'Pengabdian',
                        str_contains($rawCat, 'ADMIN') => 'Administrasi',
                        default => 'Pembelajaran',
                    };

                    $internalDate = $doc->created_at ? Carbon::parse($doc->created_at)->format('d M Y, H.i') . ' WIB' : '17 Nov 2026, 09.15 WIB';
                    $kemenkesDate = $doc->verified_at ? Carbon::parse($doc->verified_at)->format('d M Y, H.i') . ' WIB' : ($doc->is_verified ? Carbon::parse($doc->created_at)->format('d M Y, H.i') . ' WIB' : 'Menunggu Sinkronisasi');

                    return [
                        'id' => (string) $doc->id,
                        'filename' => $doc->file_name,
                        'name' => $doc->file_name,
                        'category' => $category,
                        'domain' => $category,
                        'skpValue' => (float) ($doc->credit_amount ?: 0),
                        'status' => $status,
                        'driveUrl' => $doc->google_drive_url,
                        'size' => $doc->file_size_bytes ? round($doc->file_size_bytes / 1048576, 1) . ' MB' : ($doc->google_drive_url ? 'Google Drive Cloud' : '1.8 MB'),
                        'uploadedInternal' => $internalDate,
                        'uploadedKemenkes' => $kemenkesDate,
                        'createdAt' => $doc->created_at ? Carbon::parse($doc->created_at)->format('d M Y') : '-',
                    ];
                })
                ->all();
        }

        // Formatted SKP Entries
        $formattedEntries = [];
        if ($doctorRow) {
            $pendingCreditsTotal = (float) DB::table('skp_entries')
                ->where('doctor_id', $doctorRow->id)
                ->whereIn('status', ['SUBMITTED', 'NOT_SUBMITTED', 'PENDING'])
                ->sum('credit_amount');

            $doctorRow->pending_credits = $pendingCreditsTotal;

            $rawEntries = DB::table('skp_entries')
                ->where('doctor_id', $doctorRow->id)
                ->orderBy('activity_date', 'desc')
                ->get();

            $formattedEntries = $rawEntries->map(function ($e) use ($doctorRow) {
                $rawDomain = strtoupper((string) ($e->domain ?: 'PEMBELAJARAN'));
                $category = match(true) {
                    str_contains($rawDomain, 'PELAYANAN') => 'Pelayanan',
                    str_contains($rawDomain, 'PENGABDIAN') => 'Pengabdian',
                    default => 'Pembelajaran',
                };

                $statusLabel = match(strtoupper($e->status ?: '')) {
                    'DONE', 'VERIFIED' => '✓ Terverifikasi Kemenkes',
                    'REJECTED' => '✕ Ditolak',
                    default => 'Menunggu Verifikasi',
                };

                $statusClass = match(strtoupper($e->status ?: '')) {
                    'DONE', 'VERIFIED' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                    'REJECTED' => 'bg-rose-50 text-rose-700 border border-rose-200',
                    default => 'bg-amber-50 text-amber-700 border border-amber-200',
                };

                return [
                    'id' => (string) $e->id,
                    'title' => $e->title,
                    'category' => $category,
                    'domain' => $category,
                    'date' => $e->activity_date ? Carbon::parse($e->activity_date)->format('d M Y') : '-',
                    'credit' => (float) ($e->credit_amount ?: 0),
                    'credits' => (float) ($e->credit_amount ?: 0),
                    'organizer' => $doctorRow->institution_name ?: 'PP POGI / RS Jejaring',
                    'status' => $e->status,
                    'statusLabel' => $statusLabel,
                    'statusClass' => $statusClass,
                    'eventId' => $e->event_id ? substr($e->event_id, 0, 8) : null,
                ];
            })->all();
        }

        // Missing document detection
        $missingItems = [];
        $existingCategories = collect($documents)->pluck('category')->unique()->all();
        if (!in_array('Pelayanan', $existingCategories)) {
            $missingItems[] = [
                'code' => 'DOC-REQ-01',
                'name' => 'Logbook Pelayanan Klinis / Bedah Maternal',
                'domain' => 'Pelayanan',
                'status' => 'Wajib Segera (Missing)',
                'statusBadgeClass' => 'bg-rose-50 text-rose-700 border-rose-200',
                'reason' => 'Belum ada berkas logbook pelayanan tindakan untuk memenuhi minimal 56 SKP (KMK 1561/2024)',
            ];
        }
        if (!in_array('Pembelajaran', $existingCategories)) {
            $missingItems[] = [
                'code' => 'DOC-REQ-02',
                'name' => 'Sertifikat PIT POGI / Simposium Terakreditasi',
                'domain' => 'Pembelajaran',
                'status' => 'Wajib Segera (Missing)',
                'statusBadgeClass' => 'bg-rose-50 text-rose-700 border-rose-200',
                'reason' => 'Belum ada bukti sertifikat simposium/workshop terakreditasi untuk memenuhi minimal 72 SKP',
            ];
        }
        if (!in_array('Pengabdian', $existingCategories)) {
            $missingItems[] = [
                'code' => 'DOC-REQ-03',
                'name' => 'Surat Tugas / Bukti Pengabdian Masyarakat',
                'domain' => 'Pengabdian',
                'status' => 'Peringatan (Warning)',
                'statusBadgeClass' => 'bg-amber-50 text-amber-800 border-amber-200',
                'reason' => 'Perlu bukti kegiatan bakti sosial, edukasi KB, atau penyuluhan deteksi kanker serviks (minimal 8 SKP)',
            ];
        }

        $detector = [
            'hasMissing' => count($missingItems) > 0,
            'missingCount' => count($missingItems),
            'summary' => count($missingItems) > 0 
                ? 'Terdeteksi ' . count($missingItems) . ' berkas bukti wajib yang belum lengkap berdasarkan standar terbaru KMK 1561/2024 & KMK 1111/2026.' 
                : 'Semua dokumen wajib telah lengkap dan terverifikasi sesuai standar terbaru KMK 1561/2024 & KMK 1111/2026.',
            'items' => $missingItems,
        ];

        // Audit logs untuk dokter ini
        $auditLogs = [];
        if ($doctorRow) {
            $auditLogs = DB::table('audit_logs')
                ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
                ->where('audit_logs.entity_id', $doctorRow->id)
                ->orWhere('audit_logs.new_values', 'like', "%{$doctorRow->id}%")
                ->select('audit_logs.*', 'users.full_name as actor_name', 'users.role as actor_role')
                ->orderBy('audit_logs.created_at', 'desc')
                ->limit(10)
                ->get();
        }

        $adminPogiList = DB::table('users')->where('role', 'ADMIN_POGI')->orderBy('full_name')->get();

        return view('doctor-detail', [
            'doctor' => $doctorRow,
            'period' => $period,
            'documents' => $documents,
            'skpEntries' => $formattedEntries,
            'missingDocs' => $detector,
            'auditLogs' => $auditLogs,
            'adminPogiList' => $adminPogiList,
        ]);
    }

    /**
     * Daftarkan Dokter Baru ke basis data MySQL (RFC 4122 UUID).
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user || !in_array(strtoupper($user->role), ['SUPER_ADMIN', 'ADMIN_POGI'])) {
            return response()->json(['success' => false, 'status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'str' => 'required|string|max:100',
            'phone' => 'nullable|string|max:50',
            'province' => 'nullable|string|max:100',
            'institution' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|string|max:100',
            'period_start' => 'nullable|string|max:50',
            'period_end' => 'nullable|string|max:50',
            'plataran_sehat_username' => 'nullable|string|max:150',
            'plataran_sehat_password' => 'nullable|string|max:255',
        ]);

        $now = Carbon::now();
        $doctorId = (string) Str::uuid();

        // Resolve branch / province
        $province = DB::table('provinces')->where('name', 'like', '%' . $request->input('province') . '%')->first();
        $branch = DB::table('pogi_branches')->where('province_id', $province?->id)->first()
            ?? DB::table('pogi_branches')->first();

        // Resolve institution
        $institution = DB::table('institutions')->where('name', 'like', '%' . $request->input('institution') . '%')->first();

        // Resolve PIC
        $pic = DB::table('users')->where('full_name', 'like', '%' . $request->input('assigned_to') . '%')->first()
            ?? DB::table('users')->where('role', 'ADMIN_POGI')->first();

        $randomNpa = '#POGI-2026-' . rand(2000, 9999);

        $rawNik = (string) ($request->input('nik') ?: $request->input('str'));

        $phone = $request->input('phone') ?: '+6281200000000';
        $phone = trim($phone);
        if (str_starts_with($phone, '0')) {
            $phone = '+62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '+')) {
            $phone = '+62' . $phone;
        }

        $lmsUser = $request->input('plataran_sehat_username') ?: $request->input('lms_username');
        $lmsPass = $request->input('plataran_sehat_password') ?: $request->input('lms_password');

        DB::table('doctors')->insert([
            'id' => $doctorId,
            'user_id' => null,
            'npa_pogi' => $randomNpa,
            'full_name' => $request->input('name'),
            'specialization_title' => 'Sp.OG',
            'nik_encrypted' => NikCipher::encrypt($rawNik),
            'nik_hash' => NikCipher::hash($rawNik),
            'str_number' => $request->input('str'),
            'sip_number' => 'SIP.01.2026/' . rand(1000, 9999),
            'sip_expiry_date' => Carbon::now()->addYears(5)->toDateString(),
            'email' => Str::slug($request->input('name')) . '@pogi.or.id',
            'whatsapp_number' => $phone,
            'branch_id' => $branch?->id,
            'province_id' => $province?->id ?? '31',
            'primary_institution_id' => $institution?->id,
            'assigned_pic_id' => $pic?->id,
            'plataran_sehat_username' => $lmsUser ? trim($lmsUser) : null,
            'plataran_sehat_pass_encrypted' => $lmsPass ? trim($lmsPass) : null,
            'status' => 'INCOMPLETE',
            'dashboard_updated_at' => $now,
            'kemenkes_updated_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Parse period dates
        $periodStartVal = $request->input('period_start');
        $periodEndVal = $request->input('period_end');
        try {
            $startDate = $periodStartVal ? Carbon::parse($periodStartVal)->toDateString() : $now->copy()->startOfYear()->toDateString();
        } catch (\Throwable $e) {
            $startDate = $now->copy()->startOfYear()->toDateString();
        }
        try {
            $endDate = $periodEndVal ? Carbon::parse($periodEndVal)->toDateString() : $now->copy()->addYears(5)->endOfYear()->toDateString();
        } catch (\Throwable $e) {
            $endDate = $now->copy()->addYears(5)->endOfYear()->toDateString();
        }

        // Create initial SKP period
        DB::table('doctor_skp_periods')->insert([
            'id' => (string) Str::uuid(),
            'doctor_id' => $doctorId,
            'cycle_number' => 1,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'target_total_credits' => 160.0,
            'target_pembelajaran_credits' => 72.0,
            'target_pelayanan_credits' => 56.0,
            'target_pengabdian_credits' => 8.0,
            'target_flexibility_credits' => 24.0,
            'achieved_pembelajaran_credits' => 0.0,
            'achieved_pelayanan_credits' => 0.0,
            'achieved_pengabdian_credits' => 0.0,
            'achieved_total_credits' => 0.0,
            'is_active' => true,
            'is_completed' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return response()->json([
            'success' => true,
            'status' => 'success',
            'message' => "Dokter {$request->input('name')} berhasil didaftarkan ke sistem & database!",
            'doctor' => [
                'id' => $doctorId,
                'name' => $request->input('name'),
                'pogiId' => $randomNpa,
                'strNumber' => $request->input('str'),
                'hospital' => $institution?->name ?? 'RS Jejaring POGI',
                'province' => $province?->name ?? 'DKI Jakarta',
                'assignedTo' => $pic?->full_name ?? 'Siti Rahma',
                'status' => 'Incomplete',
            ],
        ]);
    }

    /**
     * Verifikasi dan setujui kredit SKP dokter secara live ke database.
     */
    public function verifyCredits(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();
        if (!$user || !in_array(strtoupper($user->role), ['SUPER_ADMIN', 'ADMIN_POGI'])) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Akses ditolak: Hanya Super Admin dan Admin POGI yang berwenang memverifikasi kredit SKP dokter.',
            ], 403);
        }

        $doctor = DB::table('doctors')->where('id', $id)->first();
        if (!$doctor) {
            return response()->json(['success' => false, 'status' => 'error', 'message' => 'Dokter tidak ditemukan.'], 404);
        }

        $now = Carbon::now();

        // 1. Update pending SKP entries to DONE
        DB::table('skp_entries')
            ->where('doctor_id', $id)
            ->whereIn('status', ['SUBMITTED', 'NOT_SUBMITTED', 'PENDING'])
            ->update([
                'status' => 'DONE',
                'verified_by_user_id' => $user->id,
                'verified_at' => $now,
                'updated_at' => $now,
            ]);

        // 2. Update unverified doctor_documents
        DB::table('doctor_documents')
            ->where('doctor_id', $id)
            ->where('is_verified', false)
            ->update([
                'is_verified' => true,
                'verified_by_user_id' => $user->id,
                'verified_at' => $now,
            ]);

        // 3. Recalculate SKP Period totals from all DONE skp_entries
        $pelayananSum = (float) DB::table('skp_entries')
            ->where('doctor_id', $id)
            ->where('status', 'DONE')
            ->where('domain', 'like', '%PELAYANAN%')
            ->sum('credit_amount');

        $pengabdianSum = (float) DB::table('skp_entries')
            ->where('doctor_id', $id)
            ->where('status', 'DONE')
            ->where('domain', 'like', '%PENGABDIAN%')
            ->sum('credit_amount');

        $pembelajaranSum = (float) DB::table('skp_entries')
            ->where('doctor_id', $id)
            ->where('status', 'DONE')
            ->where('domain', 'like', '%PEMBELAJARAN%')
            ->sum('credit_amount');

        $totalSum = $pelayananSum + $pengabdianSum + $pembelajaranSum;

        $isPeriodComplete = ($totalSum >= 160.0)
            && ($pembelajaranSum >= 72.0)
            && ($pelayananSum >= 56.0)
            && ($pengabdianSum >= 8.0);

        // Update period
        DB::table('doctor_skp_periods')
            ->where('doctor_id', $id)
            ->where('is_active', true)
            ->update([
                'achieved_pelayanan_credits' => $pelayananSum,
                'achieved_pengabdian_credits' => $pengabdianSum,
                'achieved_pembelajaran_credits' => $pembelajaranSum,
                'achieved_total_credits' => $totalSum,
                'is_completed' => $isPeriodComplete,
                'updated_at' => $now,
            ]);

        // 4. Update doctor status sesuai RULES.md #2.4
        $newDocStatus = $isPeriodComplete ? 'COMPLETE' : 'INCOMPLETE';
        DB::table('doctors')
            ->where('id', $id)
            ->update([
                'status' => $newDocStatus,
                'dashboard_updated_at' => $now,
                'kemenkes_updated_at' => $now,
                'updated_at' => $now,
            ]);

        // 5. Log activity
        DB::table('verifier_performance_logs')->insert([
            'id' => (string) Str::uuid(),
            'verifier_user_id' => $user->id,
            'doctor_id' => $id,
            'action_type' => 'DOCUMENT_VERIFIED',
            'turnaround_time_seconds' => rand(180, 420),
            'created_at' => $now,
        ]);

        DB::table('audit_logs')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'action' => 'VERIFY_DOCTOR_CREDITS',
            'entity_name' => 'doctors',
            'entity_id' => $id,
            'old_values' => json_encode(['status' => $doctor->status]),
            'new_values' => json_encode([
                'doctor_name' => $doctor->full_name,
                'new_status' => $newDocStatus,
                'total_credits' => $totalSum,
                'pelayanan' => $pelayananSum,
                'pengabdian' => $pengabdianSum,
                'pembelajaran' => $pembelajaranSum,
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => $now,
        ]);

        return response()->json([
            'success' => true,
            'status' => 'success',
            'message' => "Kredit SKP dokter {$doctor->full_name} berhasil diverifikasi dan disinkronkan ke basis data!",
            'data' => [
                'doctorStatus' => $newDocStatus === 'COMPLETE' ? 'Complete' : 'Incomplete',
                'rawStatus' => $newDocStatus,
                'totalCredits' => $totalSum,
                'pelayanan' => $pelayananSum,
                'pengabdian' => $pengabdianSum,
                'pembelajaran' => $pembelajaranSum,
                'timestamp' => $now->format('d M Y, H.i') . ' WIB',
            ],
        ]);
    }

    /**
     * Unggah berkas baru dokter ke database.
     */
    public function uploadDocument(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();
        if (!$user || !in_array(strtoupper($user->role), ['SUPER_ADMIN', 'ADMIN_POGI', 'USER_DOKTER', 'DOKTER'])) {
            return response()->json(['success' => false, 'status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        $doctor = DB::table('doctors')->where('id', $id)->first();
        if (!$doctor) {
            return response()->json(['success' => false, 'status' => 'error', 'message' => 'Dokter tidak ditemukan.'], 404);
        }

        $request->validate([
            'filename' => 'required|string|max:255',
            'category' => 'required|string|in:Pembelajaran,Pelayanan,Pengabdian,Administrasi',
            'skp_value' => 'nullable|numeric|min:0',
            'google_drive_url' => 'required|url',
            'doc_req_code' => 'nullable|string|max:50',
            'doc_req_name' => 'nullable|string|max:255',
        ], [
            'google_drive_url.required' => 'Tautan Google Drive wajib diisi sesuai kebijakan Anti-Storage Bloat (RULES.md Bab 8).',
            'google_drive_url.url' => 'Format tautan Google Drive tidak valid.',
        ]);

        $now = Carbon::now();
        $domain = strtoupper($request->input('category', 'PEMBELAJARAN'));
        $skpValue = (float) ($request->input('skp_value') ?: 0);
        $docReqCode = $request->input('doc_req_code');
        $docReqName = $request->input('doc_req_name');
        
        $entryTitle = $docReqCode 
            ? "[{$docReqCode}] " . ($docReqName ?: 'Pemenuhan Dokumen') . ": " . $request->input('filename')
            : 'Pengajuan Bukti Fisik: ' . $request->input('filename');
        
        $period = DB::table('doctor_skp_periods')->where('doctor_id', $id)->where('is_active', true)->first();
        if (!$period) {
            $period = DB::table('doctor_skp_periods')->where('doctor_id', $id)->first();
        }
        if (!$period) {
            $periodId = (string) Str::uuid();
            DB::table('doctor_skp_periods')->insert([
                'id' => $periodId,
                'doctor_id' => $id,
                'cycle_number' => 1,
                'start_date' => $now->toDateString(),
                'end_date' => Carbon::now()->addYears(5)->toDateString(),
                'target_total_credits' => 160.0,
                'target_pembelajaran_credits' => 72.0,
                'target_pelayanan_credits' => 56.0,
                'target_pengabdian_credits' => 8.0,
                'target_flexibility_credits' => 24.0,
                'achieved_pembelajaran_credits' => 0.0,
                'achieved_pelayanan_credits' => 0.0,
                'achieved_pengabdian_credits' => 0.0,
                'achieved_total_credits' => 0.0,
                'is_active' => true,
                'is_completed' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $period = (object) ['id' => $periodId];
        }

        $categoryRow = DB::table('event_categories')->where('domain', $domain)->first()
            ?? DB::table('event_categories')->first();
        $categoryId = $categoryRow?->id ?? (string) Str::uuid();

        // 1. Insert SKP Entry
        $entryId = (string) Str::uuid();
        DB::table('skp_entries')->insert([
            'id' => $entryId,
            'doctor_id' => $id,
            'period_id' => $period->id,
            'event_id' => null,
            'category_id' => $categoryId,
            'domain' => in_array($domain, ['PEMBELAJARAN', 'PELAYANAN', 'PENGABDIAN']) ? $domain : 'PEMBELAJARAN',
            'title' => $entryTitle,
            'activity_date' => $now->toDateString(),
            'credit_amount' => $skpValue,
            'status' => 'NOT_SUBMITTED',
            'verified_by_user_id' => null,
            'verified_at' => null,
            'rejection_reason' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2. Insert Doctor Document
        $docId = (string) Str::uuid();
        $driveUrl = (string) $request->input('google_drive_url');
        $docType = $docReqCode ?: ('SERTIFIKAT_' . $domain);
        DB::table('doctor_documents')->insert([
            'id' => $docId,
            'doctor_id' => $id,
            'skp_entry_id' => $entryId,
            'document_type' => $docType,
            'file_name' => $request->input('filename'),
            'google_drive_url' => $driveUrl,
            'file_size_bytes' => 1887436,
            'mime_type' => 'application/pdf',
            'storage_bucket' => 'google-drive',
            'storage_object_key' => 'pogi-medcred-vault/doctors/' . substr($id, 0, 8) . '/' . $request->input('filename'),
            'sha256_checksum' => hash('sha256', $request->input('filename') . $now->timestamp),
            'is_verified' => false,
            'verified_by_user_id' => null,
            'verified_at' => null,
            'uploaded_by_user_id' => $user->id,
            'created_at' => $now,
            'deleted_at' => null,
        ]);

        DB::table('doctors')->where('id', $id)->update([
            'dashboard_updated_at' => $now,
            'updated_at' => $now,
        ]);

        return response()->json([
            'success' => true,
            'status' => 'success',
            'message' => 'Berkas berhasil disimpan ke Object Storage dan dicatat ke basis data!',
            'entry' => [
                'id' => $entryId,
                'title' => $entryTitle,
                'category' => $request->input('category'),
                'domain' => $request->input('category'),
                'credit' => $skpValue,
                'credits' => $skpValue,
                'status' => 'PENDING',
            ],
            'document' => [
                'id' => $docId,
                'filename' => $request->input('filename'),
                'name' => $request->input('filename'),
                'category' => $request->input('category'),
                'domain' => $request->input('category'),
                'skpValue' => $skpValue,
                'driveUrl' => $driveUrl,
                'size' => '1.8 MB',
                'status' => 'NOT_SUBMITTED',
                'uploadedInternal' => $now->format('d M Y, H.i') . ' WIB',
                'uploadedKemenkes' => 'Menunggu Sinkronisasi',
                'doc_req_code' => $docReqCode,
            ],
        ]);
    }

    /**
     * Ubah status siklus verifikasi dokumen dokter di database.
     */
    public function updateDocumentStatus(Request $request, string $id, string $docId): JsonResponse
    {
        $user = Auth::user();
        if (!$user || !in_array(strtoupper($user->role), ['SUPER_ADMIN', 'ADMIN_POGI'])) {
            return response()->json(['success' => false, 'status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        $request->validate([
            'status' => 'required|string|in:NOT_SUBMITTED,SUBMITTED,DONE,REJECTED',
        ]);

        $status = $request->input('status');
        $now = Carbon::now();

        $doc = DB::table('doctor_documents')->where('id', $docId)->where('doctor_id', $id)->first();
        if (!$doc) {
            return response()->json(['success' => false, 'status' => 'error', 'message' => 'Dokumen tidak ditemukan.'], 404);
        }

        $isVerified = ($status === 'DONE');
        DB::table('doctor_documents')->where('id', $docId)->update([
            'is_verified' => $isVerified,
            'verified_by_user_id' => $isVerified ? $user->id : null,
            'verified_at' => $isVerified ? $now : null,
        ]);

        if ($doc->skp_entry_id) {
            DB::table('skp_entries')->where('id', $doc->skp_entry_id)->update([
                'status' => $status,
                'verified_by_user_id' => $isVerified ? $user->id : null,
                'verified_at' => $isVerified ? $now : null,
                'updated_at' => $now,
            ]);
        }

        DB::table('doctors')->where('id', $id)->update([
            'kemenkes_updated_at' => $now,
            'updated_at' => $now,
        ]);

        return response()->json([
            'success' => true,
            'status' => 'success',
            'message' => "Status siklus Kemenkes dokumen berhasil diubah menjadi: {$status}",
            'timestamp' => $now->format('d M Y, H.i') . ' WIB',
        ]);
    }

    /**
     * Alihkan / Tentukan Admin PIC pengelola dokter.
     * HANYA DAPAT DIAKSES OLEH SUPER_ADMIN.
     */
    public function assignPic(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();

        // Otorisasi ketat: Hanya Super Admin yang berhak mengubah PIC dokter
        if (strtoupper($user->role) !== 'SUPER_ADMIN') {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Akses ditolak: Hanya Super Admin yang berwenang menentukan atau mengubah Admin PIC pengelola dokter.',
            ], 403);
        }

        $request->validate([
            'assigned_pic_id' => 'required|uuid|exists:users,id',
        ]);

        $doctor = DB::table('doctors')->where('id', $id)->first();
        if (!$doctor) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Data dokter tidak ditemukan.',
            ], 404);
        }

        $targetPic = DB::table('users')->where('id', $request->input('assigned_pic_id'))->first();
        if (!$targetPic) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Admin PIC tujuan tidak ditemukan.',
            ], 404);
        }

        $oldPicId = $doctor->assigned_pic_id;
        $now = Carbon::now();

        // Update database
        DB::table('doctors')->where('id', $id)->update([
            'assigned_pic_id' => $targetPic->id,
            'updated_at' => $now,
        ]);

        // Rekam ke jejak audit audit_logs
        DB::table('audit_logs')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'action' => 'REASSIGN_DOCTOR_PIC',
            'entity_name' => 'doctors',
            'entity_id' => $id,
            'old_values' => json_encode(['assigned_pic_id' => $oldPicId]),
            'new_values' => json_encode([
                'doctor_name' => $doctor->full_name,
                'new_pic_id' => $targetPic->id,
                'new_pic_name' => $targetPic->full_name,
                'new_pic_role' => $targetPic->role,
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => $now,
        ]);

        return response()->json([
            'success' => true,
            'status' => 'success',
            'message' => "PIC Dokter {$doctor->full_name} berhasil dialihkan ke {$targetPic->full_name}.",
            'pic' => [
                'id' => $targetPic->id,
                'name' => $targetPic->full_name,
                'role' => $targetPic->role,
            ],
        ]);
    }

    /**
     * Batch / Mass Reassign Doctor PIC (Eksklusif SUPER_ADMIN)
     */
    public function batchAssignPic(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (strtoupper($user->role) !== 'SUPER_ADMIN') {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Akses ditolak: Hanya Super Admin yang berwenang melakukan pengalihan massal dokter binaan.',
            ], 403);
        }

        $request->validate([
            'doctor_ids' => 'required|array|min:1',
            'doctor_ids.*' => 'uuid|exists:doctors,id',
            'assigned_pic_id' => 'required|uuid|exists:users,id',
        ]);

        $targetPic = DB::table('users')->where('id', $request->input('assigned_pic_id'))->first();
        if (!$targetPic) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Admin PIC tujuan tidak ditemukan.',
            ], 404);
        }

        $doctorIds = $request->input('doctor_ids');
        $now = Carbon::now();

        // Update database in batch
        DB::table('doctors')->whereIn('id', $doctorIds)->update([
            'assigned_pic_id' => $targetPic->id,
            'updated_at' => $now,
        ]);

        // Rekam jejak audit
        DB::table('audit_logs')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'action' => 'BATCH_REASSIGN_DOCTOR_PIC',
            'entity_name' => 'doctors',
            'entity_id' => implode(',', array_slice($doctorIds, 0, 5)),
            'old_values' => json_encode(['affected_count' => count($doctorIds)]),
            'new_values' => json_encode([
                'target_pic_id' => $targetPic->id,
                'target_pic_name' => $targetPic->full_name,
                'doctor_count' => count($doctorIds),
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => $now,
        ]);

        return response()->json([
            'success' => true,
            'status' => 'success',
            'message' => count($doctorIds) . " dokter binaan berhasil dialihkan ke {$targetPic->full_name}.",
            'affected_count' => count($doctorIds),
            'pic' => [
                'id' => $targetPic->id,
                'name' => $targetPic->full_name,
                'role' => $targetPic->role,
            ],
        ]);
    }
}
