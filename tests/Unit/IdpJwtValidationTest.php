<?php

namespace Tests\Unit;

use App\Services\IdpService;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class IdpJwtValidationTest extends TestCase
{
    private static string $trustedPrivateKey = '';

    private static string $untrustedPrivateKey = '';

    private static array $publicJwk;

    private int $originalLeeway;

    private ?int $originalTimestamp;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $options = ['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA];
        $bundledConfig = dirname(PHP_BINARY).'/extras/ssl/openssl.cnf';
        if (PHP_OS_FAMILY === 'Windows' && is_file($bundledConfig)) {
            $options['config'] = $bundledConfig;
        }
        $trustedKey = openssl_pkey_new($options);
        $untrustedKey = openssl_pkey_new($options);
        if ($trustedKey === false || $untrustedKey === false
            || ! openssl_pkey_export($trustedKey, self::$trustedPrivateKey, null, $options)
            || ! openssl_pkey_export($untrustedKey, self::$untrustedPrivateKey, null, $options)) {
            throw new RuntimeException('Could not generate ephemeral RSA keys for JWT testing.');
        }
        $details = openssl_pkey_get_details($trustedKey);
        if ($details === false || ! isset($details['rsa'])) {
            throw new RuntimeException('Could not obtain the synthetic RSA public key.');
        }
        self::$publicJwk = [
            'kty' => 'RSA', 'kid' => 'synthetic-idp-key', 'alg' => 'RS256', 'use' => 'sig',
            'n' => JWT::urlsafeB64Encode($details['rsa']['n']),
            'e' => JWT::urlsafeB64Encode($details['rsa']['e']),
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalLeeway = JWT::$leeway;
        $this->originalTimestamp = JWT::$timestamp;
        JWT::$timestamp = 1800000000;
        config([
            'cache.default' => 'array',
            'services.idp.base_url' => 'https://identity.example.test',
            'services.idp.authorize_url' => 'https://identity.example.test/authorize',
            'services.idp.token_url' => 'https://identity.example.test/token',
            'services.idp.me_url' => 'https://identity.example.test/me',
            'services.idp.jwks_url' => 'https://identity.example.test/jwks',
            'services.idp.logout_url' => 'https://identity.example.test/logout',
            'services.idp.client_id' => 'synthetic-client',
            'services.idp.client_secret' => 'synthetic-secret',
            'services.idp.callback_url' => 'https://application.example.test/auth/callback',
        ]);
        Cache::forget('idp_jwks_keys');
        Http::preventStrayRequests();
        Http::fake(['https://identity.example.test/jwks' => Http::response(['keys' => [self::$publicJwk]])]);
    }

    protected function tearDown(): void
    {
        JWT::$leeway = $this->originalLeeway;
        JWT::$timestamp = $this->originalTimestamp;
        parent::tearDown();
    }

    public function test_accepts_a_valid_token_signed_by_the_trusted_test_key(): void
    {
        $claims = (new IdpService())->validateToken($this->token());

        $this->assertSame('synthetic-student', $claims->sub);
        Http::assertSentCount(1);
    }

    public function test_rejects_a_malformed_jwt(): void
    {
        $this->assertRejected('not-a-jwt', 'Wrong number of segments');
    }

    public function test_rejects_a_modified_payload_without_a_new_signature(): void
    {
        $segments = explode('.', $this->token());
        $claims = json_decode(JWT::urlsafeB64Decode($segments[1]), true, 512, JSON_THROW_ON_ERROR);
        $claims['sub'] = 'different-synthetic-student';
        $segments[1] = JWT::urlsafeB64Encode(json_encode($claims, JSON_THROW_ON_ERROR));

        $this->assertRejected(implode('.', $segments), 'Signature verification failed');
    }

    public function test_rejects_a_token_signed_by_an_untrusted_key(): void
    {
        $this->assertRejected($this->token([], self::$untrustedPrivateKey), 'Signature verification failed');
    }

    public function test_rejects_expiration_beyond_the_five_minute_clock_tolerance(): void
    {
        $this->assertRejected($this->token(['iat' => JWT::$timestamp - 900, 'exp' => JWT::$timestamp - 301]), 'Expired token');
    }

    public function test_documents_acceptance_inside_the_existing_clock_tolerance(): void
    {
        $claims = (new IdpService())->validateToken($this->token(['iat' => JWT::$timestamp - 900, 'exp' => JWT::$timestamp - 60]));

        $this->assertSame(JWT::$timestamp - 60, $claims->exp);
        $this->assertSame(300, JWT::$leeway);
    }

    public function test_rejects_a_future_issued_at_beyond_clock_tolerance(): void
    {
        $this->assertRejected($this->token(['iat' => JWT::$timestamp + 301]), 'Cannot handle token with iat');
    }

    private function token(array $overrides = [], ?string $privateKey = null): string
    {
        return JWT::encode(array_replace([
            'sub' => 'synthetic-student',
            'iss' => 'https://identity.example.test',
            'aud' => 'synthetic-client',
            'iat' => JWT::$timestamp - 60,
            'exp' => JWT::$timestamp + 600,
        ], $overrides), $privateKey ?? self::$trustedPrivateKey, 'RS256', 'synthetic-idp-key');
    }

    private function assertRejected(string $token, string $reason): void
    {
        try {
            (new IdpService())->validateToken($token);
            $this->fail('The synthetic invalid token was accepted.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString($reason, $exception->getMessage());
            $this->assertFalse(Cache::has('idp_jwks_keys'));
        }
    }
}
