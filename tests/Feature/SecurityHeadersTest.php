<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('security.headers_enabled', true);
        config()->set('security.hsts_enabled', true);
        config()->set('security.hsts_max_age', 31536000);
        config()->set('security.csp_mode', 'report-only');
        config()->set('security.csp_policy', "default-src 'self'; object-src 'none'");

        Route::get('/_test/security-headers', fn () => response('secure'));
        Route::get('/_test/security-headers-existing-csp', fn () => response('document')->header(
            'Content-Security-Policy',
            "sandbox; default-src 'none'"
        ));
    }

    public function test_secure_responses_receive_security_headers(): void
    {
        $this->get('https://localhost/_test/security-headers')
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000')
            ->assertHeader('Content-Security-Policy-Report-Only', "default-src 'self'; object-src 'none'")
            ->assertHeaderMissing('X-Powered-By');
    }

    public function test_hsts_is_not_sent_over_plain_http(): void
    {
        $this->get('/_test/security-headers')
            ->assertOk()
            ->assertHeaderMissing('Strict-Transport-Security');
    }

    public function test_existing_stricter_csp_is_preserved(): void
    {
        $this->get('https://localhost/_test/security-headers-existing-csp')
            ->assertOk()
            ->assertHeader('Content-Security-Policy', "sandbox; default-src 'none'")
            ->assertHeaderMissing('Content-Security-Policy-Report-Only');
    }

    public function test_login_route_uses_the_named_rate_limiter(): void
    {
        $route = Route::getRoutes()->getByName('login-user');

        $this->assertNotNull($route);
        $this->assertContains('throttle:login', $route->gatherMiddleware());
    }
}
