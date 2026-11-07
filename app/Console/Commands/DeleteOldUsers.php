<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeleteOldUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-old-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disabled legacy cleanup; use the retention preview instead';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->warn('Account-age deletion is disabled. No records were deleted. Use retention:preview and the coordinator retention workspace.');

        return self::SUCCESS;
    }
}
