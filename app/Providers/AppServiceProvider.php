<?php

namespace App\Providers;

use App\Services\Uploads\ClamAvScanner;
use App\Services\Uploads\MalwareScanner;
use App\Services\Uploads\NullMalwareScanner;
use App\Services\Uploads\RejectingMalwareScanner;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

if (! function_exists('vasset')) {
    require_once app_path('Helpers/AssetHelper.php');
}

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\JwtService::class, function ($app) {
            return new \App\Services\JwtService;
        });

        $this->app->singleton(MalwareScanner::class, function () {
            if (! config('upload_security.malware_scan.enabled')) {
                if (config('upload_security.malware_scan.required')) {
                    return new RejectingMalwareScanner;
                }

                return new NullMalwareScanner;
            }

            return new ClamAvScanner(
                (string) config('upload_security.malware_scan.host'),
                (int) config('upload_security.malware_scan.port'),
                (float) config('upload_security.malware_scan.timeout')
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive('vasset', function ($expression) {
            return "<?php echo vasset($expression); ?>";
        });
    }
}
