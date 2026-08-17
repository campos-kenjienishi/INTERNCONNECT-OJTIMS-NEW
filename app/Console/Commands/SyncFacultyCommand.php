<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SyncFacultyFromFlss;

class SyncFacultyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flss:sync-faculty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync active faculty members and accounts from FLSS (Faculty Loading & Scheduling System)';

    /**
     * Execute the console command.
     */
    public function handle(SyncFacultyFromFlss $syncer): int
    {
        $this->info('Starting faculty sync from FLSS...');

        $summary = $syncer->execute();

        if (!empty($summary['errors'])) {
            foreach ($summary['errors'] as $error) {
                $this->error('Error: ' . $error);
            }
        }

        $this->info("Sync Completed: Created: {$summary['created']} | Updated: {$summary['updated']} | Skipped: {$summary['skipped']}");

        return 0;
    }
}
