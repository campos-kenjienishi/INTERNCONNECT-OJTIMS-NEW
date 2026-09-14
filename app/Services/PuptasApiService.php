<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PuptasApiService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected string $scope;
    protected int $cacheTtl;
    protected ?string $lastAuthError = null;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.puptas.base_url') ?: env('PUPTAS_BASE_URL', 'https://puptas.undraftedbsit2027.com'), '/');
        $this->clientId = config('services.puptas.client_id') ?: env('PUPTAS_CLIENT_ID', '');
        $this->clientSecret = config('services.puptas.client_secret') ?: env('PUPTAS_CLIENT_SECRET', '');
        $this->scope = config('services.puptas.scope') ?: env('PUPTAS_SCOPE', 'program-read');
        $this->cacheTtl = (int) (config('services.puptas.cache_ttl') ?: env('PUPTAS_CACHE_TTL', 86400));
    }

    /**
     * Get or generate a valid OAuth 2.0 Bearer access token (Client Credentials Grant).
     */
    public function getAccessToken(bool $forceFresh = false): ?string
    {
        if ($forceFresh) {
            Cache::forget('puptas_oauth_access_token');
        }

        $this->lastAuthError = null;

        if (empty($this->clientId) || empty($this->clientSecret)) {
            $this->lastAuthError = 'PUPTAS API configuration is missing Client ID or Secret in .env.';
            Log::warning('PUPTAS API: Missing client_id or client_secret in configuration.');
            return null;
        }

        if (Cache::has('puptas_oauth_access_token')) {
            return Cache::get('puptas_oauth_access_token');
        }

        try {
            $endpoint = $this->baseUrl . '/oauth/token';

            $response = Http::asForm()
                ->withOptions(['verify' => false])
                ->timeout(10)
                ->connectTimeout(5)
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->post($endpoint, [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'scope'         => $this->scope,
                ]);

            if ($response->successful()) {
                $json = $response->json();
                $token = $json['access_token'] ?? null;
                $expiresIn = (int) ($json['expires_in'] ?? (3600 * 12));
                if ($token) {
                    Cache::put('puptas_oauth_access_token', $token, max(60, $expiresIn - 60));
                    return $token;
                }
            }

            if ($response->status() === 401 || $response->status() === 400) {
                $this->lastAuthError = 'Failed to authenticate with PUPTAS Admission System. Please check Client ID and Secret.';
            } else {
                $this->lastAuthError = 'PUPTAS OAuth authentication returned HTTP ' . $response->status() . '.';
            }

            Log::warning('PUPTAS OAuth token request failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return null;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $this->lastAuthError = 'Unable to reach PUPTAS Admission System at ' . $this->baseUrl . ' (Connection timed out or host is unreachable).';
            Log::error('PUPTAS OAuth connection exception: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            $this->lastAuthError = 'Connection error reaching PUPTAS Admission System: ' . $e->getMessage();
            Log::error('PUPTAS OAuth token exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetch the list of active academic programs from PUPTAS.
     *
     * @param bool $forceRefresh Bypass cache and fetch fresh programs from API
     * @return array ['success' => bool, 'data' => array, 'meta' => array, 'error' => ?string]
     */
    public function getPrograms(bool $forceRefresh = false): array
    {
        $cacheKey = 'puptas_programs_cache';

        if (!$forceRefresh && Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            if (is_array($cached) && !empty($cached['data'])) {
                return array_merge($cached, ['from_cache' => true]);
            }
        }

        $token = $this->getAccessToken($forceRefresh);
        if (!$token) {
            return [
                'success' => false,
                'data'    => [],
                'meta'    => [],
                'error'   => $this->lastAuthError ?: 'Failed to authenticate with PUPTAS Admission System. Please check Client ID and Secret.',
            ];
        }

        try {
            $endpoint = $this->baseUrl . '/api/v1/programs';

            $response = Http::withOptions(['verify' => false])
                ->timeout(15)
                ->connectTimeout(6)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Accept'        => 'application/json',
                ])
                ->get($endpoint);

            // If token expired, clear cache and retry once
            if ($response->status() === 401 && !$forceRefresh) {
                Cache::forget('puptas_oauth_access_token');
                return $this->getPrograms(true);
            }

            if ($response->status() === 429) {
                return [
                    'success' => false,
                    'data'    => [],
                    'meta'    => [],
                    'error'   => 'PUPTAS rate limit exceeded (50 calls/day). Please try again later or use cached data.',
                ];
            }

            if ($response->status() === 403) {
                return [
                    'success' => false,
                    'data'    => [],
                    'meta'    => [],
                    'error'   => 'Access forbidden. Your PUPTAS client lacks the `program-read` scope.',
                ];
            }

            if ($response->successful()) {
                $json = $response->json();
                $programs = $json['data'] ?? [];
                $meta = $json['meta'] ?? [];

                $result = [
                    'success'    => true,
                    'data'       => $programs,
                    'meta'       => $meta,
                    'error'      => null,
                    'from_cache' => false,
                ];

                // Cache the response to strictly respect the 50 req/day limit
                Cache::put($cacheKey, $result, $this->cacheTtl);

                return $result;
            }

            Log::error('PUPTAS getPrograms failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return [
                'success' => false,
                'data'    => [],
                'meta'    => [],
                'error'   => 'PUPTAS API returned error (HTTP ' . $response->status() . '): ' . $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('PUPTAS getPrograms exception: ' . $e->getMessage());
            return [
                'success' => false,
                'data'    => [],
                'meta'    => [],
                'error'   => 'Connection error reaching PUPTAS Admission System: ' . $e->getMessage(),
            ];
        }
    }
}
