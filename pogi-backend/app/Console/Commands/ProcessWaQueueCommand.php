<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ProcessWaQueueCommand extends Command
{
    protected $signature = 'pogi:wa-process-queue {--limit=15 : Maksimal 15 pesan/menit (RULES.md Bab 4)}';
    protected $description = 'Proses antrean wa_message_queue dengan anti-ban jitter 1500-3000ms (maks 15 pesan/menit)';

    public function handle(): int
    {
        $limit = min(15, max(1, (int) $this->option('limit')));
        $gatewayUrl = env('WHATSAPP_GATEWAY_URL');
        $gatewayToken = env('WHATSAPP_GATEWAY_TOKEN');

        $messages = DB::table('wa_message_queue')
            ->where('status', 'QUEUED')
            ->orderBy('created_at')
            ->limit($limit)
            ->get();

        if ($messages->isEmpty()) {
            return self::SUCCESS;
        }

        $sentCount = 0;
        foreach ($messages as $item) {
            $jitterMs = max(1500, min(3000, (int) ($item->jitter_ms ?: rand(1500, 3000))));
            usleep($jitterMs * 1000);

            $status = 'SENT';
            if (!empty($gatewayUrl)) {
                try {
                    $resp = Http::withToken((string) $gatewayToken)
                        ->timeout(10)
                        ->post($gatewayUrl, [
                            'to' => $item->phone_number,
                            'message' => $item->message_body,
                        ]);
                    $status = $resp->successful() ? 'SENT' : 'FAILED';
                } catch (\Throwable $e) {
                    $status = 'FAILED';
                }
            }

            DB::table('wa_message_queue')
                ->where('id', $item->id)
                ->update([
                    'status' => $status,
                    'sent_at' => Carbon::now('Asia/Jakarta'),
                    'updated_at' => Carbon::now('Asia/Jakarta'),
                ]);

            $sentCount++;
        }

        $this->info("Berhasil memproses {$sentCount} pesan antrean WA.");
        return self::SUCCESS;
    }
}
