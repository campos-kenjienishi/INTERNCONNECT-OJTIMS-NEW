<?php

namespace App\Console\Commands;

use App\Models\User; // Import the User model
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
    protected $description = 'Delete users older than one year';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $yearAgo = now()->subYear();
        User::where('created_at', '<=', $yearAgo)
            ->where('role', 0) // 0 indicates student role
            ->delete();

        $this->info('Old student users deleted successfully.');
    }
}
