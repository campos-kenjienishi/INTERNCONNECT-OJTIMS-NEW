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
        if (config('announcement_email.enabled')) {
            $schedule->command('announcements:send-emails')->everyMinute()->withoutOverlapping(10);
        }
        $schedule->command('app:delete-old-users')->daily(); // Corrected command signature
        $schedule->command('flss:sync-faculty')->monthlyOn(1, '02:00')->withoutOverlapping();

        if (config('audit.prune_enabled', true)) {
            $schedule->command('app:prune-audit-logs')
                ->dailyAt('01:30')
                ->withoutOverlapping();
        }

        if (config('upload_security.integrity_check_enabled', true)) {
            $schedule->command('uploads:verify-security')
                ->weeklyOn(1, '02:30')
                ->withoutOverlapping();
        }
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
