<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('document-downloads', function (Request $request) {
            return Limit::perMinute((int) config('upload_security.download_rate_per_minute', 60))
                ->by((string) ($request->session()->get('loginId') ?: $request->ip()));
        });

        RateLimiter::for('document-previews', function (Request $request) {
            return Limit::perMinute((int) config('upload_security.preview_rate_per_minute', 120))
                ->by((string) ($request->session()->get('loginId') ?: $request->ip()));
        });

        RateLimiter::for('login', function (Request $request) {
            $identity = Str::lower(trim((string) $request->input('email')));

            return [
                Limit::perMinute((int) config('security.login_attempts_per_minute', 5))
                    ->by($identity.'|'.$request->ip()),
                Limit::perMinute((int) config('security.login_ip_attempts_per_minute', 30))
                    ->by($request->ip()),
            ];
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
