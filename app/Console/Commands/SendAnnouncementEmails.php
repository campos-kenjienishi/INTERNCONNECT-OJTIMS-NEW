<?php

namespace App\Console\Commands;

use App\Services\AnnouncementEmailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SendAnnouncementEmails extends Command
{
    protected $signature = 'announcements:send-emails {--limit= : Maximum emails to process (1-100)}';

    protected $description = 'Process the announcement email outbox; local and testing environments capture email only';

    public function handle(AnnouncementEmailService $emails): int
    {
        if (!config('announcement_email.enabled') || !$emails->ready()) {
            $this->warn('Announcement email is disabled or its migration is missing. No email was processed.');
            return self::SUCCESS;
        }
        $limit = filter_var($this->option('limit') ?? config('announcement_email.batch_size'), FILTER_VALIDATE_INT);
        if ($limit === false || $limit < 1 || $limit > 100) {
            $this->error('Limit must be between 1 and 100.');
            return self::FAILURE;
        }
        $lock = Cache::lock('announcements:send-emails', 600);
        if (!$lock->get()) {
            $this->info('Another announcement email worker is running.');
            return self::SUCCESS;
        }
        try {
            $emails->markInterrupted();
            $ids = DB::table('announcement_email_deliveries')->where('status', 'queued')->orderBy('id')->limit($limit)->pluck('id');
            $results = [];
            $started = microtime(true);
            foreach ($ids as $id) {
                if (microtime(true) - $started > 50) {
                    break;
                }
                $status = $emails->deliver($id);
                $results[$status] = ($results[$status] ?? 0) + 1;
                $delay = max(0, min(10000, (int) config('announcement_email.interval_ms', 1000)));
                if ($delay > 0) {
                    usleep($delay * 1000);
                }
            }
            $this->info($emails->captureOnly() ? 'Capture mode: no real recipient email sent.' : 'Live mail transport enabled. Sent means transport acceptance, not inbox delivery.');
            $this->line(json_encode($results, JSON_THROW_ON_ERROR));
            return self::SUCCESS;
        } finally {
            $lock->release();
        }
    }
}
