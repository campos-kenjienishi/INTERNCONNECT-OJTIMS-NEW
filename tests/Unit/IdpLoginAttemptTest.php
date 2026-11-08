<?php

namespace Tests\Unit;

use App\Services\IdpLoginAttempt;
use Illuminate\Http\Request;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

class IdpLoginAttemptTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['services.idp.enforce_state' => true, 'services.idp.allow_missing_state' => false,
            'services.idp.login_attempt_seconds' => 600]);
        Cache::flush();
    }

    public function test_begin_replaces_the_pending_attempt(): void
    {
        $this->freezeTime();
        $request = $this->request();
        $guard = new IdpLoginAttempt();
        $previous = $guard->begin($request);
        $current = $guard->begin($request);

        $this->assertNotSame($previous, $current);
        $this->assertSame(64, strlen($current));
        $this->assertSame($current, $request->session()->get('idp_oauth_state'));
        $this->assertSame(now()->timestamp, $request->session()->get('idp_oauth_started_at'));
    }

    public function test_matching_state_is_accepted_and_consumed(): void
    {
        $request = $this->request(['code' => 'synthetic-code', 'state' => str_repeat('a', 64)]);
        $this->pending($request);

        $this->assertSame('state', (new IdpLoginAttempt())->consume($request));
        $this->assertFalse($request->session()->has('idp_oauth_state'));
        $this->assertFalse($request->session()->has('idp_oauth_started_at'));
        $this->expectException(RuntimeException::class);
        (new IdpLoginAttempt())->consume($request);
    }

    public function test_code_only_requires_explicit_compatibility(): void
    {
        $request = $this->request(['code' => 'synthetic-code']);
        $this->pending($request);
        $this->expectException(RuntimeException::class);
        (new IdpLoginAttempt())->consume($request);
    }

    public function test_compatibility_accepts_only_an_initiated_code_only_callback(): void
    {
        config(['services.idp.allow_missing_state' => true]);
        $request = $this->request(['code' => 'synthetic-code']);
        $this->pending($request);

        $this->assertSame('code_only', (new IdpLoginAttempt())->consume($request));
        $this->expectException(RuntimeException::class);
        (new IdpLoginAttempt())->consume($this->request(['code' => 'another-code']));
    }

    public static function rejectedCallbacks(): array
    {
        return [
            'mismatched state' => [['code' => 'code', 'state' => 'wrong'], 0],
            'empty state' => [['code' => 'code', 'state' => ''], 0],
            'array state' => [['code' => 'code', 'state' => ['wrong']], 0],
            'missing code' => [[], 0],
            'empty code' => [['code' => ''], 0],
            'array code' => [['code' => ['wrong']], 0],
            'oversized code' => [['code' => str_repeat('a', 2049)], 0],
            'whitespace code' => [['code' => "bad\ncode"], 0],
            'expired attempt' => [['code' => 'code'], 600],
            'future attempt' => [['code' => 'code'], -1],
        ];
    }

    #[DataProvider('rejectedCallbacks')]
    public function test_compatibility_still_rejects_invalid_callbacks(array $query, int $age): void
    {
        config(['services.idp.allow_missing_state' => true]);
        $request = $this->request($query);
        $this->pending($request, $age);
        try {
            (new IdpLoginAttempt())->consume($request);
            $this->fail('Invalid callback was accepted.');
        } catch (RuntimeException) {
            $this->assertFalse($request->session()->has('idp_oauth_state'));
            $this->assertFalse($request->session()->has('idp_oauth_started_at'));
        }
    }

    public function test_legacy_flag_does_not_bypass_the_pending_attempt_requirement(): void
    {
        config(['services.idp.enforce_state' => false]);
        $request = $this->request(['code' => 'code']);
        $this->pending($request);
        $this->assertSame('code_only', (new IdpLoginAttempt())->consume($request));

        $this->expectException(RuntimeException::class);
        (new IdpLoginAttempt())->consume($this->request(['code' => 'other-code']));
    }

    public function test_cache_blocks_reuse_even_with_a_copy_of_the_pending_session(): void
    {
        $first = $this->request(['code' => 'first-code', 'state' => str_repeat('a', 64)]);
        $second = $this->request(['code' => 'second-code', 'state' => str_repeat('a', 64)]);
        $this->pending($first);
        $this->pending($second);
        (new IdpLoginAttempt())->consume($first);

        $this->expectException(RuntimeException::class);
        (new IdpLoginAttempt())->consume($second);
    }

    public function test_code_cannot_be_reused_in_a_new_attempt(): void
    {
        config(['services.idp.allow_missing_state' => true]);
        $first = $this->request(['code' => 'reused-code']);
        $second = $this->request(['code' => 'reused-code']);
        $this->pending($first);
        $this->pending($second);
        $second->session()->put('idp_oauth_state', str_repeat('b', 64));
        (new IdpLoginAttempt())->consume($first);

        $this->expectException(RuntimeException::class);
        (new IdpLoginAttempt())->consume($second);
    }

    private function request(array $query = []): Request
    {
        $request = Request::create('/auth/callback', 'GET', $query);
        $session = new Store('idp-test', new ArraySessionHandler(120));
        $session->start();
        $request->setLaravelSession($session);

        return $request;
    }

    private function pending(Request $request, int $age = 0): void
    {
        $request->session()->put('idp_oauth_state', str_repeat('a', 64));
        $request->session()->put('idp_oauth_started_at', now()->timestamp - $age);
    }
}
