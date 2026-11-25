<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RunWaReminderCommand extends Command
{
    protected $signature = 'pogi:wa-reminder {--trigger=H-7 : H-7 atau H-2}';
    protected $description = 'Jadwalkan antrean WhatsApp Blast otomatis H-7 & H-2 menjelang cutoff ';

    public function handle(): int
    {
        $now = Carbon::now('Asia/Jakarta');
        $trigger = strtoupper((string) $this->option('trigger')) === 'H-2' ? 'H-2' : 'H-7';

        $doctors = DB::table('doctors')
            ->leftJoin('doctor_skp_periods as p', function ($j) {
                $j->on('doctors.id', '=', 'p.doctor_id')->where('p.is_active', true);
            })
            ->where('doctors.status', '!=', 'COMPLETE')
            ->select(
                'doctors.id',
                'doctors.full_name',
                'doctors.whatsapp_number',
                DB::raw('COALESCE(p.achieved_total_credits, 0) as total_credits')
            )
            ->get();

        $campaignId = (string) Str::uuid();
        DB::table('wa_blast_campaigns')->insert([
            'id' => $campaignId,
            'title' => "Pengingat Otomatis {$trigger} Cutoff SKP (" . $now->format('M Y') . ')',
            'trigger_type' => $trigger,
            'target_filters' => json_encode(['status' => 'INCOMPLETE', 'channels' => ['whatsapp']]),
            'total_recipients' => $doctors->count(),
            'status' => 'QUEUED',
            'created_by_user_id' => DB::table('users')->where('role', 'SUPER_ADMIN')->value('id'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $queueRows = [];
        foreach ($doctors as $doc) {
            $shortfall = max(0, 160 - (float) $doc->total_credits);
            $msg = "Yth. {$doc->full_name}, pengingat {$trigger} cutoff bulanan POGI SKP Tracker: kekurangan kumulatif Anda saat ini {$shortfall} SKP. Mohon unggah tautan Google Drive bukti kegiatan melalui portal anggota.";

            $queueRows[] = [
                'id' => (string) Str::uuid(),
                'campaign_id' => $campaignId,
                'doctor_id' => $doc->id,
                'phone_number' => $doc->whatsapp_number ?: '+6281200000000',
                'message_body' => $msg,
                'status' => 'QUEUED',
                'jitter_ms' => rand(1500, 3000),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($queueRows)) {
            foreach (array_chunk($queueRows, 100) as $chunk) {
                DB::table('wa_message_queue')->insert($chunk);
            }
        }

        DB::table('audit_logs')->insert([
            'id' => (string) Str::uuid(),
            'user_id' => null,
            'action' => "CRON_TRIGGER_{$trigger}",
            'entity_name' => 'wa_blast_campaigns',
            'entity_id' => $campaignId,
            'new_values' => json_encode([
                'message' => "Otomasi {$trigger}: {$doctors->count()} dokter status Incomplete dimasukkan ke antrean WA dengan jitter 1500-3000ms.",
            ]),
            'user_agent' => 'Laravel Scheduler (pogi:wa-reminder)',
            'created_at' => $now,
        ]);

        $this->info("WA Reminder {$trigger}: {$doctors->count()} pesan masuk ke wa_message_queue.");
        return self::SUCCESS;
    }
}
