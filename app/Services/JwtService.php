<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use UnexpectedValueException;

class JwtService {
    /**
     * Generate a JWT for the given user.
     *
     * @param \App\Models\User $user
     * @return string JWT token
     */
    public function generate($user): string 
    {
        $secret = config('auth.jwt.secret');
        $algo   = config('auth.jwt.algo', 'HS256');
        $issuer = config('auth.jwt.issuer', 'student-services-system');
        $aud    = config('auth.jwt.audience', 'student-services-system');
        $azp    = config('auth.jwt.azp', 'student-services-system');
        $now    = time();
        $exp    = $now + 3600; // 1 hour expiry
        $payload = [
            'iss' => $issuer,
            'aud' => $aud,
            'azp' => $azp,
            'iat' => $now,
            'nbf' => $now,
            'exp' => $exp,
            'userId' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'roles' => $user->roles()->pluck('name')->toArray(),
        ];
        return JWT::encode($payload, $secret, $algo);
    }

    /**
     * Decode and validate the JWT from the external auth server.
     *
     * Validates: signature, exp, nbf, iss, aud, azp.
     * Also enforces one-time use via jti (replay prevention).
     *
     * @return object Decoded JWT payload
     * @throws \RuntimeException on any validation failure
     */
    public function decode(string $token): object
    {
        $secret = config('auth.jwt.secret');
        $algo   = config('auth.jwt.algo', 'HS256');

        try {
            $payload = JWT::decode($token, new Key($secret, $algo));
        } catch (ExpiredException $e) {
            Log::warning('JWT callback: token expired', ['error' => $e->getMessage()]);
            throw new \RuntimeException('Authentication token has expired.');
        } catch (BeforeValidException $e) {
            Log::warning('JWT callback: token not yet valid', ['error' => $e->getMessage()]);
            throw new \RuntimeException('Authentication token is not yet valid.');
        } catch (SignatureInvalidException $e) {
            Log::warning('JWT callback: invalid signature');
            throw new \RuntimeException('Authentication token signature is invalid.');
        } catch (UnexpectedValueException $e) {
            Log::warning('JWT callback: malformed token', ['error' => $e->getMessage()]);
            throw new \RuntimeException('Authentication token is malformed.');
        }

        $this->validateClaims($payload);
        $this->preventReplay($payload);

        return $payload;
    }

    /**
     * Validate issuer, audience, and authorized party claims.
     */
    private function validateClaims(object $payload): void
    {
        $expectedIss = config('auth.jwt.issuer');
        $expectedAud = config('auth.jwt.audience');
        $expectedAzp = config('auth.jwt.azp');

        if ($expectedIss && ($payload->iss ?? null) !== $expectedIss) {
            Log::warning('JWT callback: issuer mismatch', [
                'expected' => $expectedIss,
                'got'      => $payload->iss ?? null,
            ]);
            throw new \RuntimeException('Authentication token issuer is invalid.');
        }

        if ($expectedAud) {
            $aud = $payload->aud ?? null;
            $audValues = is_array($aud) ? $aud : [$aud];
            if (!in_array($expectedAud, $audValues, true)) {
                Log::warning('JWT callback: audience mismatch');
                throw new \RuntimeException('Authentication token audience is invalid.');
            }
        }

        if ($expectedAzp && ($payload->azp ?? null) !== $expectedAzp) {
            Log::warning('JWT callback: azp mismatch');
            throw new \RuntimeException('Authentication token authorized party is invalid.');
        }

        if (empty($payload->userId)) {
            throw new \RuntimeException('Authentication token is missing user identifier.');
        }
    }

    /**
     * Prevent replay attacks by ensuring each jti is used only once.
     * The jti is cached for the token's remaining lifetime.
     */
    private function preventReplay(object $payload): void
    {
        $jti = $payload->jti ?? null;
        if (!$jti) {
            return; // jti is optional; skip replay check if absent
        }

        $cacheKey = 'jwt_jti_' . hash('sha256', $jti);
        $ttlSeconds = max(0, ($payload->exp ?? 0) - time());

        if (Cache::has($cacheKey)) {
            Log::warning('JWT callback: replayed token', ['jti' => $jti]);
            throw new \RuntimeException('Authentication token has already been used.');
        }

        Cache::put($cacheKey, true, $ttlSeconds + 60);
    }
}
