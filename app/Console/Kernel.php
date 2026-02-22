<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('app:delete-old-users')->daily(); // Corrected command signature
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    protected $routeMiddleware = [
        // Other middleware entries...
        'auth' => \App\Http\Middleware\AuthMiddleware::class,
        'coordinator' => \App\Http\Middleware\OJTCoordinatorMiddleware::class,
    ];
    
}
