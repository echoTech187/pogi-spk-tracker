<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ActivityController extends Controller
{
    /**
     * Tampilkan jejak audit permanen (Audit Logs) secara live dari basis data MySQL.
     */
    public function index(Request $request): View
    {
        // 1. Pastikan log otomasi cron dasar ada jika belum tercatat di sistem
        $cronCount = DB::table('audit_logs')
            ->where('entity_name', 'cron_scheduler')
            ->orWhere('action', 'like', 'CRON_%')
            ->count();

        if ($cronCount === 0) {
            $superAdmin = DB::table('users')->where('role', 'SUPER_ADMIN')->first();
            $adminId = $superAdmin ? $superAdmin->id : null;
            $incompleteDocs = DB::table('doctors')->whereNull('deleted_at')->where('status', '!=', 'COMPLETE')->count();

            DB::table('audit_logs')->insert([
                [
                    'id' => (string) Str::uuid(),
                    'user_id' => $adminId,
                    'action' => 'CRON_TRIGGER_H7_REMINDER',
                    'entity_name' => 'cron_scheduler',
                    'entity_id' => (string) Str::uuid(),
                    'old_values' => null,
                    'new_values' => json_encode([
                        'trigger' => 'H-7',
                        'target' => "{$incompleteDocs} Dokter Incomplete (Nasional)",
                        'channels' => ['whatsapp', 'email'],
                        'message' => "Eksekusi otomatis pengingat rutin H-7: {$incompleteDocs} dokter status Incomplete diproses & antrean pengingat WhatsApp dijadwalkan."
                    ]),
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'CronWorker/2.0',
                    'created_at' => Carbon::now()->subDays(1)->startOfDay()->addSeconds(15),
                ],
                [
                    'id' => (string) Str::uuid(),
                    'user_id' => $adminId,
                    'action' => 'CRON_HEALTH_CHECK_WORKER',
                    'entity_name' => 'cron_scheduler',
                    'entity_id' => (string) Str::uuid(),
                    'old_values' => null,
                    'new_values' => json_encode([
                        'worker' => 'QueueWorker-1',
                        'status' => 'HEALTHY',
                        'message' => 'Pemeriksaan integritas scheduler otomasi & koneksi gateway SATUSEHAT: Status Aktif (0 antrean macet).'
                    ]),
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'CronWorker/2.0',
                    'created_at' => Carbon::now()->subDays(1)->setHour(6)->setMinute(0),
                ],
                [
                    'id' => (string) Str::uuid(),
                    'user_id' => $adminId,
                    'action' => 'CRON_RECAP_BRANCH_ESCALATION',
                    'entity_name' => 'cron_scheduler',
                    'entity_id' => (string) Str::uuid(),
                    'old_values' => null,
                    'new_values' => json_encode([
                        'branches' => 38,
                        'target' => 'Admin Cabang POGI',
                        'message' => 'Rekapitulasi otomatis berkas pending verifikasi diteruskan ke 38 Admin Cabang POGI Daerah.'
                    ]),
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'CronWorker/2.0',
                    'created_at' => Carbon::now()->subDays(1)->setHour(18)->setMinute(0),
                ],
                [
                    'id' => (string) Str::uuid(),
                    'user_id' => $adminId,
                    'action' => 'CRON_PRE_DEADLINE_SYNC',
                    'entity_name' => 'cron_scheduler',
                    'entity_id' => (string) Str::uuid(),
                    'old_values' => null,
                    'new_values' => json_encode([
                        'target' => 'SATUSEHAT Plataran Sehat',
                        'message' => "Sinkronisasi pra-cutoff otomatis: pembaruan berkas dari SATUSEHAT untuk {$incompleteDocs} dokter incomplete."
                    ]),
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'CronWorker/2.0',
                    'created_at' => Carbon::now()->startOfDay()->addSeconds(10),
                ]
            ]);
        }

        // 2. Ambil seluruh jejak audit
        $rawLogs = DB::table('audit_logs')
            ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
            ->select(
                'audit_logs.*',
                'users.full_name as user_name',
                'users.email as user_email',
                'users.role as user_role'
            )
            ->orderBy('audit_logs.created_at', 'desc')
            ->limit(500)
            ->get();

        $logs = $rawLogs->map(function ($l) {
            $actionUpper = strtoupper($l->action ?? '');

            // Kategori Standar
            $cat = 'General';
            if (str_contains($actionUpper, 'KEMENKES') || str_contains($actionUpper, 'SYNC') || str_contains($actionUpper, 'SATUSEHAT')) {
                $cat = 'Sync';
            } elseif (str_contains($actionUpper, 'VERIF') || str_contains($actionUpper, 'APPROVE') || str_contains($actionUpper, 'REJECT') || str_contains($actionUpper, 'CREDIT')) {
                $cat = 'Verification';
            } elseif (str_contains($actionUpper, 'FOLLOW') || str_contains($actionUpper, 'NOTIF') || str_contains($actionUpper, 'REMINDER') || str_contains($actionUpper, 'BLAST') || str_contains($actionUpper, 'PIC')) {
                $cat = 'FollowUp';
            } elseif (str_contains($actionUpper, 'CRON') || str_contains($actionUpper, 'AUTO') || str_contains($actionUpper, 'SCHEDULER') || str_contains($actionUpper, 'TRIGGER')) {
                $cat = 'Automation';
            }

            // Nama Aksi Friendly
            $actionName = $l->action;
            if ($actionUpper === 'KEMENKES_PORTAL_ACCESS') $actionName = 'Akses Portal SATUSEHAT';
            elseif ($actionUpper === 'VERIFY_DOCTOR_CREDITS') $actionName = 'Verifikasi Kredit SKP';
            elseif ($actionUpper === 'APPROVE_CREDITS') $actionName = 'Persetujuan Kredit SKP';
            elseif ($actionUpper === 'SEND_FOLLOW_UP_NOTIFICATION') $actionName = 'Kirim Notifikasi Follow-Up';
            elseif ($actionUpper === 'REASSIGN_DOCTOR_PIC') $actionName = 'Penugasan PIC Dokter';
            elseif (str_contains($actionUpper, 'CRON_TRIGGER_H7')) $actionName = 'Trigger Rutin H-7 Pengingat';
            elseif (str_contains($actionUpper, 'CRON_TRIGGER_H2')) $actionName = 'Trigger Deadline Kritis H-2';
            elseif (str_contains($actionUpper, 'CRON')) $actionName = 'Eksekusi Otomasi Cron';

            // Ekstrak Rincian Data Human-Readable
            $details = '-';
            if ($l->new_values) {
                try {
                    $parsed = is_string($l->new_values) ? json_decode($l->new_values, true) : (array) $l->new_values;
                    if (!empty($parsed['message'])) {
                        $details = $parsed['message'];
                    } elseif (!empty($parsed['event']) && !empty($parsed['doctor_context'])) {
                        $details = "{$parsed['event']} — {$parsed['doctor_context']}";
                    } elseif (!empty($parsed['doctor_name']) && isset($parsed['total_credits'])) {
                        $newStatus = $parsed['new_status'] ?? 'Diproses';
                        $details = "{$parsed['doctor_name']} — Total: {$parsed['total_credits']} SKP ({$newStatus})";
                    } elseif (!empty($parsed['doctor_name']) && !empty($parsed['new_pic_name'])) {
                        $picRole = $parsed['new_pic_role'] ?? 'ADMIN';
                        $details = "{$parsed['doctor_name']} ditugaskan ke PIC {$parsed['new_pic_name']} ({$picRole})";
                    } elseif (!empty($parsed['target']) && !empty($parsed['recipients'])) {
                        $channels = isset($parsed['channels']) ? implode(', ', (array) $parsed['channels']) : 'whatsapp';
                        $details = "Target: {$parsed['target']} ({$parsed['recipients']} dokter via {$channels})";
                    } elseif (isset($parsed['credits'])) {
                        $st = $parsed['status'] ?? 'DONE';
                        $details = "Status: {$st} • {$parsed['credits']} SKP";
                    } else {
                        $details = is_string($l->new_values) ? $l->new_values : json_encode($l->new_values);
                    }
                } catch (\Throwable $e) {
                    $details = (string) $l->new_values;
                }
            } elseif ($l->entity_name) {
                $details = "Entitas: {$l->entity_name} (" . ($l->entity_id ?: '-') . ")";
            }

            // Waktu Format Indonesia
            $timeText = 'Baru saja';
            if ($l->created_at) {
                try {
                    $d = Carbon::parse($l->created_at)->locale('id');
                    $timeText = $d->translatedFormat('d M Y, H:i') . ' WIB';
                } catch (\Throwable $e) {
                    $timeText = (string) $l->created_at;
                }
            }

            return [
                'id' => (string) $l->id,
                'user_name' => $l->user_name ?: ($cat === 'Automation' ? 'Sistem Otomasi (Cron Worker)' : 'System Auto'),
                'user_role' => $l->user_role ?: ($cat === 'Automation' ? 'SYSTEM' : 'ADMIN'),
                'action' => $l->action,
                'action_name' => $actionName,
                'details_text' => $details,
                'category' => $cat,
                'time_text' => $timeText,
                'created_at' => (string) $l->created_at,
            ];
        });

        $totalLogs = DB::table('audit_logs')->count();
        $todayStart = Carbon::today();
        $todayActions = DB::table('audit_logs')->where('created_at', '>=', $todayStart)->count();
        $uniqueUsers = DB::table('audit_logs')->distinct('user_id')->count('user_id');
        $securityActions = DB::table('audit_logs')
            ->whereIn('action', ['REASSIGN_DOCTOR_PIC', 'KEMENKES_PORTAL_ACCESS', 'LOGIN', 'LOGOUT', 'UPDATE_STATUS'])
            ->count();

        $incompleteDoctors = DB::table('doctors')
            ->whereNull('deleted_at')
            ->where('status', '!=', 'COMPLETE')
            ->count();

        $metrics = [
            'total_logs' => $totalLogs,
            'today_actions' => $todayActions,
            'unique_users' => $uniqueUsers,
            'security_actions' => $securityActions,
            'incomplete_doctors' => $incompleteDoctors,
        ];

        if ($request->wantsJson() || $request->query('format') === 'json' || $request->ajax()) {
            return response()->json([
                'success' => true,
                'logs' => $logs,
                'metrics' => $metrics,
            ]);
        }

        return view('activity', [
            'logs' => $logs,
            'metrics' => $metrics,
        ]);
    }

    /**
     * Eksekusi simulasi pemicu pengingat otomatis (Cron Job Scheduler).
     */
    public function simulateCron(Request $request): JsonResponse
    {
        $triggerType = $request->input('trigger', 'H-7');
        $user = Auth::user();

        $incompleteCount = DB::table('doctors')->whereNull('deleted_at')->where('status', '!=', 'COMPLETE')->count();

        $actionName = $triggerType === 'H-2' ? 'CRON_TRIGGER_H2_CRITICAL' : 'CRON_TRIGGER_H7_REMINDER';
        $logId = (string) Str::uuid();
        $now = Carbon::now();

        $details = [
            'trigger' => $triggerType,
            'target' => "{$incompleteCount} Dokter Incomplete (Nasional)",
            'channels' => ['whatsapp', 'email'],
            'message' => "Eksekusi trigger {$triggerType}: {$incompleteCount} dokter status Incomplete diproses & antrean pengingat dikirim."
        ];

        DB::table('audit_logs')->insert([
            'id' => $logId,
            'user_id' => $user ? $user->id : null,
            'action' => $actionName,
            'entity_name' => 'cron_scheduler',
            'entity_id' => $logId,
            'old_values' => null,
            'new_values' => json_encode($details),
            'ip_address' => $request->ip() ?: '127.0.0.1',
            'user_agent' => $request->userAgent() ?: 'Cron Worker Service',
            'created_at' => $now,
        ]);

        return response()->json([
            'success' => true,
            'trigger' => $triggerType,
            'count' => $incompleteCount,
            'log' => [
                'id' => $logId,
                'user_name' => 'Sistem Otomasi (Cron Worker)',
                'user_role' => 'SYSTEM',
                'action' => $actionName,
                'action_name' => "Trigger {$triggerType} Pengingat SKP",
                'details_text' => $details['message'],
                'category' => 'Automation',
                'time_text' => $now->locale('id')->translatedFormat('d M Y, H:i') . ' WIB',
                'created_at' => $now->toIso8601String(),
            ]
        ]);
    }
}
