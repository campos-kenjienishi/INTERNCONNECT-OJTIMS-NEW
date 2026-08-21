<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

if (!function_exists('vasset')) {
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
            return new \App\Services\JwtService();
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
