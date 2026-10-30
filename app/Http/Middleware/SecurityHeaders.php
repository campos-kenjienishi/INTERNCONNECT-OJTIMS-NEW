<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!config('security.headers_enabled', true)) {
            return $response;
        }

        $this->removeTechnologyDisclosure($response);
        $this->setIfMissing($response, 'X-Content-Type-Options', 'nosniff');
        $this->setIfMissing($response, 'X-Frame-Options', 'SAMEORIGIN');
        $this->setIfMissing($response, 'Referrer-Policy', 'strict-origin-when-cross-origin');

        if ($request->isSecure() && config('security.hsts_enabled', true)) {
            $this->setIfMissing(
                $response,
                'Strict-Transport-Security',
                'max-age='.(int) config('security.hsts_max_age', 31536000)
            );
        }

        $this->applyContentSecurityPolicy($response);

        return $response;
    }

    private function applyContentSecurityPolicy(Response $response): void
    {
        if ($response->headers->has('Content-Security-Policy')) {
            return;
        }

        $policy = trim((string) config('security.csp_policy'));
        $mode = strtolower((string) config('security.csp_mode', 'report-only'));

        if ($policy === '' || $mode === 'off') {
            return;
        }

        $header = $mode === 'enforce'
            ? 'Content-Security-Policy'
            : 'Content-Security-Policy-Report-Only';

        $this->setIfMissing($response, $header, $policy);
    }

    private function removeTechnologyDisclosure(Response $response): void
    {
        $response->headers->remove('X-Powered-By');

        if (!headers_sent()) {
            header_remove('X-Powered-By');
        }
    }

    private function setIfMissing(Response $response, string $header, string $value): void
    {
        if (!$response->headers->has($header)) {
            $response->headers->set($header, $value);
        }
    }
}
