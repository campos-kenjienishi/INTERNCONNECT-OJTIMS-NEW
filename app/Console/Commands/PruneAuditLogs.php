<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use Illuminate\Console\Command;

class PruneAuditLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:prune-audit-logs {--days= : Override retention period in days} {--dry-run : Preview how many logs would be removed without deleting them}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete audit logs older than the configured retention period.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $retentionDays = (int) ($this->option('days') ?: config('audit.retention_days', 730));

        if ($retentionDays <= 0) {
            $this->error('Audit log retention days must be greater than zero.');
            return self::FAILURE;
        }

        $cutoff = now()->subDays($retentionDays);
        $query = AuditLog::where('created_at', '<', $cutoff);
        $count = (clone $query)->count();

        if ($this->option('dry-run')) {
            $this->info("Dry run: {$count} audit log(s) older than {$cutoff->toDateTimeString()} would be deleted.");
            return self::SUCCESS;
        }

        if ($count === 0) {
            $this->info('No audit logs matched the current retention policy.');
            return self::SUCCESS;
        }

        $query->delete();

        $this->info("Deleted {$count} audit log(s) older than {$cutoff->toDateTimeString()}.");

        return self::SUCCESS;
    }
}
