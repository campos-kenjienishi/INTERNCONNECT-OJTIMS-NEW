<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use RuntimeException;

class IdpLoginAttempt
{
    public function begin(Request $request): string
    {
        $state = Str::random(64);
        $request->session()->put('idp_oauth_state', $state);
        $request->session()->put('idp_oauth_started_at', now()->timestamp);

        return $state;
    }

    public function consume(Request $request): string
    {
        $expectedState = $request->session()->pull('idp_oauth_state');
        $startedAt = $request->session()->pull('idp_oauth_started_at');
        $providedState = $request->query('state');
        $code = $request->query('code');
        $ttl = max(60, min(900, (int) config('services.idp.login_attempt_seconds', 600)));
        $age = is_int($startedAt) ? now()->timestamp - $startedAt : null;

        if (! is_string($expectedState) || strlen($expectedState) !== 64
            || $age === null || $age < 0 || $age >= $ttl
            || ! is_string($code) || $code === '' || strlen($code) > 2048
            || preg_match('/[\x00-\x20\x7f]/', $code)) {
            throw new RuntimeException('Invalid or expired IdP login attempt.');
        }

        $mode = 'state';
        if ($request->query->has('state')) {
            if (! is_string($providedState) || ! hash_equals($expectedState, $providedState)) {
                throw new RuntimeException('IdP state mismatch.');
            }
        } else {
            $allowMissingState = config('services.idp.allow_missing_state', false)
                || ! config('services.idp.enforce_state', true);
            if (! $allowMissingState) {
                throw new RuntimeException('IdP state was not returned.');
            }
            $mode = 'code_only';
        }

        if (! Cache::add('idp_login_attempt:'.hash('sha256', $expectedState), true, now()->addSeconds(900))) {
            throw new RuntimeException('IdP login attempt already consumed.');
        }
        if (! Cache::add('idp_login_code:'.hash('sha256', $code), true, now()->addSeconds(900))) {
            throw new RuntimeException('IdP authorization code already presented.');
        }

        return $mode;
    }
}
