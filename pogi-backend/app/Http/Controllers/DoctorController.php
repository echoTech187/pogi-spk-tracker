<?php

namespace App\Http\Controllers;

use App\Domain\Entities\SystemRole;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
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

        // Isolasi Cabang Otomatis untuk ADMIN_CABANG
        if ($user && $user->role === SystemRole::ADMIN_CABANG) {
            $branchId = $user->branch_id;
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

        // Query dokter dengan relasi lengkap
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
            ->whereNull('doctors.deleted_at')
            ->when($branchId, fn($q) => $q->where('doctors.branch_id', $branchId));

        // Filter status jika ada
        if ($request->filled('status') && $request->query('status') !== 'ALL') {
            $query->where('doctors.status', $request->query('status'));
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

            return [
                'id' => (string) $d->id,
                'pogiId' => $d->npa_pogi ? "#{$d->npa_pogi}" : "#POGI-" . substr($d->id, 0, 8),
                'name' => $d->full_name,
                'specialization' => $d->specialization_title ?: 'Spesialis Obstetri & Ginekologi',
                'initials' => $initials,
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
                'status' => $d->status ?: 'INCOMPLETE',
                'achievedCredits' => $achieved,
                'targetCredits' => $target,
                'progressPct' => $pct,
                'q4Credits' => round($achieved * 0.25, 1),
                'pelayananCredits' => (float) ($d->achieved_pelayanan_credits ?: 0),
                'pengabdianCredits' => (float) ($d->achieved_pengabdian_credits ?: 0),
                'pembelajaranCredits' => (float) ($d->achieved_pembelajaran_credits ?: 0),
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
    public function show(Request $request): View
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

            if ($user && $user->isAdminCabang() && $user->branch_id) {
                $fallbackQuery->where('doctors.branch_id', $user->branch_id);
            }

            $doctorRow = $fallbackQuery->first();
        }

        // Isolasi multi-tenant ketat untuk ADMIN_CABANG
        if ($user && $user->isAdminCabang()) {
            if ($doctorRow && $doctorRow->branch_id !== $user->branch_id) {
                abort(403, 'Akses ditolak: Anda hanya berwenang memantau dokter di wilayah cabang Anda sendiri.');
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

        // Doctor Documents
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

                    return [
                        'id' => (string) $doc->id,
                        'filename' => $doc->file_name,
                        'name' => $doc->file_name,
                        'category' => $doc->domain ?: ($doc->document_type ?: 'Pembelajaran'),
                        'skpValue' => (float) ($doc->credit_amount ?: 0),
                        'status' => $status,
                        'driveUrl' => $doc->google_drive_url,
                        'size' => $doc->file_size_bytes ? round($doc->file_size_bytes / 1048576, 1) . ' MB' : ($doc->google_drive_url ? 'Google Drive Cloud' : '1.8 MB'),
                        'createdAt' => $doc->created_at ? Carbon::parse($doc->created_at)->format('d M Y') : '-',
                    ];
                })
                ->all();
        }

        // SKP Entries
        $skpEntries = [];
        if ($doctorRow) {
            $skpEntries = DB::table('skp_entries')
                ->where('doctor_id', $doctorRow->id)
                ->orderBy('activity_date', 'desc')
                ->get();
        }

        // Missing document detection
        $missingDocs = [];
        $docTypes = collect($documents)->pluck('category')->all();
        if (!in_array('Pelayanan', $docTypes)) {
            $missingDocs[] = ['title' => 'Logbook Pelayanan Klinis / Bedah', 'category' => 'Pelayanan', 'urgency' => 'Mandatori'];
        }
        if (!in_array('Pembelajaran', $docTypes)) {
            $missingDocs[] = ['title' => 'Sertifikat PIT POGI / Simposium Terakreditasi', 'category' => 'Pembelajaran', 'urgency' => 'Mandatori'];
        }
        if (!in_array('Pengabdian', $docTypes)) {
            $missingDocs[] = ['title' => 'Surat Tugas Pengabdian Masyarakat', 'category' => 'Pengabdian', 'urgency' => 'Waspada'];
        }

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
            'skpEntries' => $skpEntries,
            'missingDocs' => $missingDocs,
            'auditLogs' => $auditLogs,
            'adminPogiList' => $adminPogiList,
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
}
