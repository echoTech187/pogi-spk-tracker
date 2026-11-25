<?php

namespace App\Http\Controllers;

use App\Application\UseCases\Doctor\GetDashboardSummaryUseCase;
use App\Domain\Entities\SystemRole;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly GetDashboardSummaryUseCase $getDashboardSummaryUseCase
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $branchId = null;

        // Multi-tenant automatic branch scoping for Admin Cabang & PIC scoping for Admin POGI
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

        $summary = $this->getDashboardSummaryUseCase->execute($branchId, $picUserId);

        return view('dashboard', [
            'summary' => $summary,
            'user' => $user,
        ]);
    }

    /**
     * Kirimkan follow-up pemenuhan SKP ke antrean pesan database riil (wa_blast_campaigns & wa_message_queue).
     */
    public function sendFollowUp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'target' => 'required|string',
            'channels' => 'nullable|array',
            'message' => 'required|string',
        ]);

        $user = $request->user();
        $target = $validated['target'];
        $messageTemplate = $validated['message'];
        $channels = $validated['channels'] ?? ['whatsapp'];

        // Query dokter target dari basis data MySQL live
        $query = DB::table('doctors')
            ->leftJoin('pogi_branches', 'doctors.branch_id', '=', 'pogi_branches.id')
            ->leftJoin('provinces', 'doctors.province_id', '=', 'provinces.id')
            ->whereNull('doctors.deleted_at')
            ->where('doctors.status', '!=', 'COMPLETE');

        // Admin POGI hanya mengirim blast ke dokter binaan PIC-nya sendiri
        $roleStr = $user?->role instanceof SystemRole ? $user->role->value : strtoupper((string) ($user?->role ?? ''));
        if ($roleStr === 'ADMIN_POGI') {
            $query->where('doctors.assigned_pic_id', $user->id);
        }

        $targetLabel = 'Semua Dokter Incomplete';

        if ($target === 'lowest_5') {
            // Ambil 5 cabang dengan kepatuhan terendah
            $lowestBranches = DB::table('pogi_branches')
                ->leftJoin('doctors', 'pogi_branches.id', '=', 'doctors.branch_id')
                ->select(
                    'pogi_branches.id',
                    DB::raw('COUNT(doctors.id) as total_docs'),
                    DB::raw('SUM(CASE WHEN doctors.status = "COMPLETE" THEN 1 ELSE 0 END) as complete_docs')
                )
                ->groupBy('pogi_branches.id')
                ->havingRaw('COUNT(doctors.id) > 0')
                ->orderByRaw('(SUM(CASE WHEN doctors.status = "COMPLETE" THEN 1 ELSE 0 END) / COUNT(doctors.id)) ASC')
                ->limit(5)
                ->pluck('id')
                ->all();

            $query->whereIn('doctors.branch_id', $lowestBranches);
            $targetLabel = '5 Cabang Kepatuhan Terendah';
        } elseif ($target !== 'all_incomplete') {
            $matchedBranch = DB::table('pogi_branches')
                ->where('branch_code', $target)
                ->orWhere('id', $target)
                ->orWhere('branch_name', 'like', "%{$target}%")
                ->first();

            if ($matchedBranch) {
                $query->where('doctors.branch_id', $matchedBranch->id);
                $targetLabel = "Cabang {$matchedBranch->branch_name}";
            }
        }

        $targetDoctors = $query->select(
            'doctors.id',
            'doctors.full_name',
            'doctors.whatsapp_number',
            'doctors.email',
            'pogi_branches.branch_name',
            'provinces.name as province_name'
        )->get();

        $campaignId = (string) Str::uuid();
        $totalRecipients = $targetDoctors->count();

        // 1. Simpan kampanye ke wa_blast_campaigns
        DB::table('wa_blast_campaigns')->insert([
            'id' => $campaignId,
            'title' => "Follow-Up Pemenuhan SKP - {$targetLabel}",
            'trigger_type' => 'MANUAL_DASHBOARD',
            'template_type' => 'FOLLOW_UP_REMINDER',
            'message_template' => $messageTemplate,
            'target_filters' => json_encode(['target' => $target, 'channels' => $channels]),
            'total_recipients' => $totalRecipients,
            'successful_sends' => 0,
            'failed_sends' => 0,
            'created_by_user_id' => $user?->id,
            'created_at' => Carbon::now(),
        ]);

        // 2. Masukkan ke wa_message_queue riil
        $now = Carbon::now();
        $queueRows = [];
        foreach ($targetDoctors as $doc) {
            $personalMsg = str_replace(
                ['[Nama Dokter]', '[Provinsi]', '[Cabang]'],
                [$doc->full_name, $doc->province_name ?? '-', $doc->branch_name ?? '-'],
                $messageTemplate
            );

            $queueRows[] = [
                'id' => (string) Str::uuid(),
                'campaign_id' => $campaignId,
                'doctor_id' => $doc->id,
                'recipient_phone' => $doc->whatsapp_number ?: '+628123456789',
                'recipient_name' => $doc->full_name,
                'message_body' => $personalMsg,
                'status' => 'QUEUED',
                'retry_count' => 0,
                'jitter_ms' => rand(2500, 6000),
                'scheduled_at' => $now,
                'created_at' => $now,
            ];
        }

        if (!empty($queueRows)) {
            foreach (array_chunk($queueRows, 50) as $chunk) {
                DB::table('wa_message_queue')->insert($chunk);
            }
        }

        // 3. Catat di audit_logs
        DB::table('audit_logs')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => $user?->id,
            'action' => 'SEND_FOLLOW_UP_NOTIFICATION',
            'entity_name' => 'wa_blast_campaigns',
            'entity_id' => $campaignId,
            'new_values' => json_encode([
                'target' => $targetLabel,
                'recipients' => $totalRecipients,
                'channels' => $channels,
            ]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => $now,
        ]);

        return response()->json([
            'success' => true,
            'campaign_id' => $campaignId,
            'recipients_count' => $totalRecipients,
            'target_label' => $targetLabel,
            'message' => "Notifikasi berhasil dijadwalkan ke antrean database untuk {$totalRecipients} dokter.",
        ]);
    }
}
