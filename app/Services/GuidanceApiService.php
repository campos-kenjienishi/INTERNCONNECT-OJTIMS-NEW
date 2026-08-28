<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GuidanceApiService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.guisis.base_url') ?: env('GUISIS_BASE_URL', 'https://api-guisis.dllbsit2027.com/api/v1'), '/');
        $this->clientId = config('services.guisis.client_id') ?: env('GUISIS_CLIENT_ID', '40284c44-2d44-42ce-88bc-b4e5cc9e8be7');
        $this->clientSecret = config('services.guisis.client_secret') ?: env('GUISIS_CLIENT_SECRET', '6f99e1b82ddb71d40b5f5dc30d7ef8e755455982c2bbb92ba68322278adf05fa');
    }

    /**
     * Get or generate a valid M2M access token.
     */
    public function getM2MToken(): ?string
    {
        return Cache::remember('guisis_m2m_token', 3000, function () {
            try {
                $endpoint = $this->baseUrl . '/auth/m2m/token';
                $response = Http::withOptions(['verify' => false])
                    ->timeout(6)
                    ->connectTimeout(3)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'Accept'       => 'application/json',
                    ])
                    ->post($endpoint, [
                        'clientId'     => $this->clientId,
                        'clientSecret' => $this->clientSecret,
                    ]);

                if ($response->successful()) {
                    $json = $response->json();
                    return $json['data']['accessToken'] ?? $json['accessToken'] ?? null;
                }

                Log::warning('GuiSIS M2M token request failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            } catch (\Exception $e) {
                Log::error('GuiSIS M2M token exception: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Build HTTP headers with Bearer token.
     */
    protected function getHeaders(): array
    {
        $token = $this->getM2MToken();
        $headers = [
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ];

        if ($token) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        return $headers;
    }

    /**
     * Check if GuiSIS API is reachable and healthy.
     */
    public function isReachable(): bool
    {
        try {
            $token = $this->getM2MToken();
            if (!empty($token)) {
                return true;
            }

            $healthRes = Http::withOptions(['verify' => false])
                ->timeout(3)
                ->connectTimeout(2)
                ->get($this->baseUrl . '/health');

            return $healthRes->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Fetch a paginated page of student profiles.
     * Endpoint: GET /integrations/students/profiles
     */
    public function getProfiles(int $page = 1, int $pageSize = 50): ?array
    {
        try {
            $endpoint = $this->baseUrl . '/integrations/students/profiles';
            $headers = $this->getHeaders();

            $response = Http::withOptions(['verify' => false])
                ->timeout(8)
                ->connectTimeout(3)
                ->withHeaders($headers)
                ->get($endpoint, [
                    'page'     => $page,
                    'pageSize' => $pageSize,
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('GuiSIS getProfiles request failed', [
                'page'   => $page,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('GuiSIS getProfiles exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetch all student profiles across all pages (cached for 30 minutes).
     */
    public function getAllStudentProfiles(bool $forceRefresh = false): array
    {
        if ($forceRefresh) {
            Cache::forget('guisis_all_student_profiles');
        }

        return Cache::remember('guisis_all_student_profiles', 1800, function () {
            $allProfiles = [];
            $page = 1;
            $pageSize = 50;

            do {
                $res = $this->getProfiles($page, $pageSize);
                if (!$res) {
                    break;
                }

                // GuiSIS returns {"status": "success", "data": {"students": [...], "meta": {"totalPages": 28}}}
                $studentsList = $res['data']['students'] ?? $res['data']['profiles'] ?? $res['data']['data'] ?? $res['data'] ?? [];
                if (empty($studentsList) || !is_array($studentsList)) {
                    break;
                }

                foreach ($studentsList as $profile) {
                    if (is_array($profile) && (isset($profile['studentNumber']) || isset($profile['idpUuid']) || isset($profile['email']))) {
                        $allProfiles[] = $profile;
                    }
                }

                $meta = $res['data']['meta'] ?? $res['meta'] ?? [];
                $totalPages = $meta['totalPages'] ?? $meta['total_pages'] ?? null;

                if ($totalPages && $page >= $totalPages) {
                    break;
                }

                $page++;
                if ($page > 50) { // Safety limit
                    break;
                }
            } while (true);

            return $allProfiles;
        });
    }

    /**
     * Find single student profile by UUID, email, or name with early exit and cache fallback.
     */
    public function findStudentProfile(?string $idpUuid = null, ?string $email = null, ?string $name = null): ?array
    {
        $idpUuid = strtolower(trim($idpUuid ?? ''));
        $email = strtolower(trim($email ?? ''));
        $cleanName = strtolower(preg_replace('/[^a-z0-9]/', '', $name ?? ''));

        // 1. Check cached pool first
        $cached = Cache::get('guisis_all_student_profiles');
        if (is_array($cached) && !empty($cached)) {
            foreach ($cached as $p) {
                $pUuid = strtolower(trim($p['idpUuid'] ?? ''));
                $pEmail = strtolower(trim($p['email'] ?? ''));
                $pName = strtolower(preg_replace('/[^a-z0-9]/', '', ($p['firstName'] ?? '') . ' ' . ($p['lastName'] ?? '')));

                if (($idpUuid && $pUuid === $idpUuid) ||
                    ($email && $pEmail === $email) ||
                    ($cleanName && $pName === $cleanName)) {
                    return $p;
                }
            }
        }

        // 2. Fetch page by page with early exit and cache accumulation
        @set_time_limit(120);
        $page = 1;
        $pageSize = 50;
        $maxPages = 35;
        $accumulated = [];
        $matchedProfile = null;

        while ($page <= $maxPages) {
            $res = $this->getProfiles($page, $pageSize);
            if (!$res) {
                break;
            }

            $studentsList = $res['data']['students'] ?? $res['data']['profiles'] ?? $res['data']['data'] ?? $res['data'] ?? [];
            if (empty($studentsList) || !is_array($studentsList)) {
                break;
            }

            foreach ($studentsList as $p) {
                if (!is_array($p)) continue;
                $accumulated[] = $p;

                if (!$matchedProfile) {
                    $pUuid = strtolower(trim($p['idpUuid'] ?? ''));
                    $pEmail = strtolower(trim($p['email'] ?? ''));
                    $pName = strtolower(preg_replace('/[^a-z0-9]/', '', ($p['firstName'] ?? '') . ' ' . ($p['lastName'] ?? '')));

                    if (($idpUuid && $pUuid === $idpUuid) ||
                        ($email && $pEmail === $email) ||
                        ($cleanName && $pName === $cleanName)) {
                        $matchedProfile = $p;
                        // Return matched profile immediately!
                        Cache::put('guisis_all_student_profiles', $accumulated, 1800);
                        return $matchedProfile;
                    }
                }
            }

            $meta = $res['data']['meta'] ?? $res['meta'] ?? [];
            $totalPages = $meta['totalPages'] ?? $meta['total_pages'] ?? null;
            if ($totalPages && $page >= $totalPages) {
                break;
            }

            $page++;
        }

        if (!empty($accumulated)) {
            Cache::put('guisis_all_student_profiles', $accumulated, 1800);
        }

        return $matchedProfile;
    }

    /**
     * Endpoint: GET /integrations/students/{studentNumber}
     */
    public function getStudentByNumber(string $studentNumber): ?array
    {
        if (empty($studentNumber)) {
            return null;
        }

        try {
            $endpoint = $this->baseUrl . '/integrations/students/' . urlencode($studentNumber);
            $headers = $this->getHeaders();

            $response = Http::withOptions(['verify' => false])
                ->timeout(5)
                ->connectTimeout(2)
                ->withHeaders($headers)
                ->get($endpoint);

            if ($response->successful()) {
                $json = $response->json();
                return $json['data'] ?? $json;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('GuiSIS getStudentByNumber exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetch student address records by student number.
     * Endpoint: GET /integrations/students/{studentNumber}/addresses
     */
    public function getAddresses(string $studentNumber): ?array
    {
        if (empty($studentNumber)) {
            return null;
        }

        try {
            $endpoint = $this->baseUrl . '/integrations/students/' . urlencode($studentNumber) . '/addresses';
            $headers = $this->getHeaders();

            $response = Http::withOptions(['verify' => false])
                ->timeout(5)
                ->connectTimeout(2)
                ->withHeaders($headers)
                ->get($endpoint);

            if ($response->successful()) {
                $json = $response->json();
                return $json['data'] ?? $json;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('GuiSIS getAddresses exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetch student personal information (DOB, gender, emergency contacts) by student number.
     * Endpoint: GET /integrations/students/{studentNumber}/personal-info
     */
    public function getPersonalInfo(string $studentNumber): ?array
    {
        if (empty($studentNumber)) {
            return null;
        }

        try {
            $endpoint = $this->baseUrl . '/integrations/students/' . urlencode($studentNumber) . '/personal-info';
            $headers = $this->getHeaders();

            $response = Http::withOptions(['verify' => false])
                ->timeout(5)
                ->connectTimeout(2)
                ->withHeaders($headers)
                ->get($endpoint);

            if ($response->successful()) {
                $json = $response->json();
                return $json['data'] ?? $json;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('GuiSIS getPersonalInfo exception: ' . $e->getMessage());
            return null;
        }
    }
}
