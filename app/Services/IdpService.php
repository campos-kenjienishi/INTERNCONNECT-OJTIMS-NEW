<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
use Firebase\JWT\Key;
use UnexpectedValueException;

class IdpService
{
    protected string $baseUrl;
    protected string $authorizeUrl;
    protected string $tokenUrl;
    protected string $meUrl;
    protected string $jwksUrl;
    protected string $logoutUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected string $callbackUrl;
    protected bool $verifyTls;

    public function __construct()
    {
        $this->baseUrl      = config('services.idp.base_url');
        $this->authorizeUrl = config('services.idp.authorize_url');
        $this->tokenUrl     = config('services.idp.token_url');
        $this->meUrl        = config('services.idp.me_url');
        $this->jwksUrl      = config('services.idp.jwks_url');
        $this->logoutUrl    = config('services.idp.logout_url');
        $this->clientId     = config('services.idp.client_id');
        $this->clientSecret = config('services.idp.client_secret');
        $this->callbackUrl  = config('services.idp.callback_url');
        $this->verifyTls    = config('services.idp.verify_tls', true);
    }

    /**
     * Build the URL to redirect the user to the IDP's authorization page.
     */
    public function getAuthorizeUrl(): string
    {
        return $this->authorizeUrl . '?' . http_build_query([
            'client_id'    => $this->clientId,
            'redirect_uri' => $this->callbackUrl,
        ]);
    }

    /**
     * Exchange the authorization code for access and refresh tokens.
     *
     * @param  string  $code  The authorization code from the IDP callback.
     * @return array  Token response: { access_token, refresh_token, expires_in, token_type, scope }
     *
     * @throws \RuntimeException
     */
    public function exchangeCode(string $code): array
    {
        $response = Http::withOptions(['verify' => $this->verifyTls])
            ->acceptJson()
            ->post($this->tokenUrl, [
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code'          => $code,
                'grant_type'    => 'authorization_code',
            ]);

        if ($response->failed()) {
            Log::error('IDP token exchange failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \RuntimeException('Failed to exchange authorization code with Identity Provider.');
        }

        return $response->json();
    }

    /**
     * Fetch the authenticated user's profile from the IDP.
     *
     * @param  string  $accessToken  The JWT access token.
     * @return array  User info: { id, email, first_name, last_name, middle_name, name_suffix, roles }
     *
     * @throws \RuntimeException
     */
    public function getUserInfo(string $accessToken): array
    {
        $response = Http::withOptions(['verify' => $this->verifyTls])
            ->withToken($accessToken)
            ->acceptJson()
            ->get($this->meUrl);

        if ($response->failed()) {
            Log::error('IDP /me request failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \RuntimeException('Failed to fetch user profile from Identity Provider.');
        }

        return $response->json();
    }

    /**
     * Fetch the JWKS (JSON Web Key Set) from the IDP for JWT validation.
     * Results are cached for 1 hour to avoid repeated HTTP calls.
     *
     * @return array  The parsed JWKS keys.
     */
    public function fetchJwks(): array
    {
        return Cache::remember('idp_jwks_keys', 3600, function () {
            $response = Http::withOptions(['verify' => $this->verifyTls])
                ->acceptJson()
                ->get($this->jwksUrl);

            if ($response->failed()) {
                Log::error('IDP JWKS fetch failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                throw new \RuntimeException('Failed to fetch JWKS from Identity Provider.');
            }

            return $response->json();
        });
    }

    /**
     * Validate a JWT access token using the IDP's JWKS public keys.
     *
     * @param  string  $jwt  The raw JWT token string.
     * @return object  The decoded JWT payload.
     *
     * @throws \RuntimeException
     */
    public function validateToken(string $jwt): object
    {
        try {
            $jwks = $this->fetchJwks();
            $keysList = $jwks['keys'] ?? [];

            if (empty($keysList)) {
                throw new \RuntimeException('No public keys found in JWKS from Identity Provider.');
            }

            // Handle IdP JWKS keys that have an empty string or missing "kid"
            $keys = [];
            foreach ($keysList as $index => $item) {
                $kid = (!empty($item['kid'])) ? $item['kid'] : 'default_' . $index;
                $item['kid'] = $kid;

                try {
                    $keys[$kid] = JWK::parseKey($item);
                } catch (\Exception $e) {
                    Log::warning('Failed to parse JWK key item', ['index' => $index, 'error' => $e->getMessage()]);
                }
            }

            if (empty($keys)) {
                throw new \RuntimeException('Could not parse any valid public key from Identity Provider JWKS.');
            }

            // Check if JWT header has a kid or if we can decode directly with single key fallback
            $tks = explode('.', $jwt);
            if (count($tks) === 3) {
                $header = json_decode(JWT::urlsafeB64Decode($tks[0]));
                $jwtKid = $header->kid ?? null;

                if ((!$jwtKid || !isset($keys[$jwtKid])) && count($keys) === 1) {
                    $singleKey = reset($keys);
                    return JWT::decode($jwt, $singleKey);
                }
            }

            return JWT::decode($jwt, $keys);

        } catch (UnexpectedValueException $e) {
            Log::warning('IDP JWT validation failed', ['error' => $e->getMessage()]);
            Cache::forget('idp_jwks_keys');
            throw new \RuntimeException('Identity Provider token validation failed: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('IDP JWT validation error', ['error' => $e->getMessage()]);
            Cache::forget('idp_jwks_keys');
            throw new \RuntimeException('Identity Provider token validation error: ' . $e->getMessage());
        }
    }

    /**
     * Call the IDP's global logout endpoint.
     *
     * @param  string  $accessToken  The access token to revoke.
     */
    public function logout(string $accessToken): void
    {
        try {
            Http::withOptions(['verify' => $this->verifyTls])
                ->withToken($accessToken)
                ->acceptJson()
                ->post($this->logoutUrl, [
                    'client_id' => $this->clientId,
                ]);
        } catch (\Exception $e) {
            // Log but don't throw — local logout should still proceed
            Log::warning('IDP logout request failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Check if the IDP service is reachable.
     *
     * @return bool
     */
    public function isReachable(): bool
    {
        try {
            $response = Http::withOptions([
                'verify'  => $this->verifyTls,
                'timeout' => 5,
            ])->get($this->jwksUrl);

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Obtain an access token using the Client Credentials grant type.
     * This token is used for server-to-server calls (e.g., admin user list)
     * and does not require a user to log in interactively.
     *
     * @return string  The access token.
     *
     * @throws \RuntimeException
     */
    public function getClientCredentialsToken(): string
    {
        // 1. Check if an admin access token is provided in .env or session or cache
        $storedToken = config('services.idp.admin_access_token')
            ?: session('idp_access_token')
            ?: Cache::get('idp_admin_access_token');

        if (!empty($storedToken)) {
            return $storedToken;
        }

        // 2. Otherwise request via client_credentials grant
        $response = Http::withOptions(['verify' => $this->verifyTls])
            ->acceptJson()
            ->post($this->tokenUrl, [
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type'    => 'client_credentials',
            ]);

        if ($response->failed()) {
            Log::error('IDP client_credentials token request failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \RuntimeException('Failed to obtain admin access token from Identity Provider.');
        }

        $token = $response->json('access_token');

        if (empty($token)) {
            throw new \RuntimeException('Identity Provider returned empty access token for client_credentials grant.');
        }

        return $token;
    }

    /**
     * Fetch a paginated list of all users from the IDP admin API.
     *
     * @param  string  $accessToken  Admin-scoped access token.
     * @param  int     $page         Page number (1-based).
     * @param  int     $limit        Number of records per page.
     * @return array   The parsed JSON response from the IDP.
     *
     * @throws \RuntimeException
     */
    public function getAdminUserList(string $accessToken, int $page = 1, int $limit = 100): array
    {
        $url = $this->baseUrl . '/api/v1/admin/users?' . http_build_query([
            'page'    => $page,
            'limit'   => $limit,
            'sort_by' => 'created_at',
            'order'   => 'desc',
        ]);

        $sessionCookie = config('services.idp.session_cookie') ?: Cache::get('idp_session_cookie');

        $request = Http::withOptions(['verify' => $this->verifyTls])
            ->withToken($accessToken)
            ->acceptJson()
            ->timeout(30);

        if (!empty($sessionCookie)) {
            $request = $request->withHeaders([
                'Cookie'   => 'session_cookie=' . $sessionCookie . '; access_token=' . $accessToken,
                'Referer'  => $this->baseUrl . '/user-pool',
            ]);
        }

        $response = $request->get($url);

        if ($response->failed()) {
            Log::error('IDP admin user list request failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
                'page'   => $page,
            ]);
            throw new \RuntimeException('Failed to fetch user list from Identity Provider admin API (page ' . $page . ').');
        }

        return $response->json() ?? [];
    }
}
