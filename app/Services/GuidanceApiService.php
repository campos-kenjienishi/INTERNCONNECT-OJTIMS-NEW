<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GuidanceApiService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $clientId;
    protected string $clientSecret;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.guisis.base_url', 'https://api-guisis.dllbsit2027.com/api/v1'), '/');
        $this->apiKey = config('services.guisis.api_key', '');
        $this->clientId = config('services.guisis.client_id', '');
        $this->clientSecret = config('services.guisis.client_secret', '');
    }

    /**
     * Build HTTP headers for GuiSIS requests.
     */
    protected function getHeaders(): array
    {
        $headers = [];

        if (!empty($this->clientId)) {
            $headers['X-Client-ID'] = $this->clientId;
        }

        if (!empty($this->clientSecret)) {
            $headers['X-Client-Secret'] = $this->clientSecret;
        }

        if (!empty($this->apiKey)) {
            $headers['Authorization'] = 'Bearer ' . $this->apiKey;
            $headers['X-API-Key'] = $this->apiKey;
        }

        return $headers;
    }

    /**
     * List students with pagination and optional filters.
     * Endpoint: GET /students/external
     */
    public function listStudents(int $page = 1, int $pageSize = 50, array $filters = []): ?array
    {
        try {
            $endpoint = $this->baseUrl . '/students/external';
            $headers = $this->getHeaders();
            $request = Http::timeout(5)->connectTimeout(2)->acceptJson();

            if (!empty($headers)) {
                $request = $request->withHeaders($headers);
            }

            $queryParams = array_merge([
                'page' => $page,
                'page_size' => $pageSize,
            ], $filters);

            $response = $request->get($endpoint, $queryParams);

            if ($response->successful()) {
                $data = $response->json();
                return is_array($data) ? $data : null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('GuiSIS listStudents exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get core student record by email address.
     * Endpoint: GET /students/external/by-email/{email}
     */
    public function getStudentByEmail(string $email): ?array
    {
        if (empty($email)) {
            return null;
        }

        try {
            $endpoint = $this->baseUrl . '/students/external/by-email/' . urlencode($email);
            
            $headers = $this->getHeaders();
            $request = Http::timeout(4)->connectTimeout(2)->acceptJson();

            if (!empty($headers)) {
                $request = $request->withHeaders($headers);
            }

            $response = $request->get($endpoint);

            if ($response->successful()) {
                $data = $response->json();
                return is_array($data) ? $data : null;
            }

            // If the route doesn't exist on the GuiSIS server yet, throw DomainException for circuit breaking
            if ($response->status() === 404 && str_contains($response->body(), 'Endpoint not found')) {
                throw new \DomainException('GuiSIS student lookup endpoint (/students/external/by-email) is not available on GuiSIS API.');
            }

            Log::warning('GuiSIS API lookup returned non-200 status', [
                'email' => $email,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;

        } catch (\DomainException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('GuiSIS API lookup exception', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get student personal/demographic info by student number.
     * Endpoint: GET /students/external/personal-info/{studentNumber}
     */
    public function getPersonalInfo(string $studentNumber): ?array
    {
        if (empty($studentNumber)) {
            return null;
        }

        try {
            $endpoint = $this->baseUrl . '/students/external/personal-info/' . urlencode($studentNumber);

            $headers = $this->getHeaders();
            $request = Http::timeout(4)->connectTimeout(2)->acceptJson();

            if (!empty($headers)) {
                $request = $request->withHeaders($headers);
            }

            $response = $request->get($endpoint);

            if ($response->successful()) {
                $data = $response->json();
                return is_array($data) ? $data : null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('GuiSIS API personal info exception', [
                'studentNumber' => $studentNumber,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get student address records by student number.
     * Endpoint: GET /students/external/addresses/{studentNumber}
     */
    public function getAddresses(string $studentNumber): ?array
    {
        if (empty($studentNumber)) {
            return null;
        }

        try {
            $endpoint = $this->baseUrl . '/students/external/addresses/' . urlencode($studentNumber);

            $headers = $this->getHeaders();
            $request = Http::timeout(4)->connectTimeout(2)->acceptJson();

            if (!empty($headers)) {
                $request = $request->withHeaders($headers);
            }

            $response = $request->get($endpoint);

            if ($response->successful()) {
                $data = $response->json();
                return is_array($data) ? $data : null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('GuiSIS API addresses exception', [
                'studentNumber' => $studentNumber,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Check if GuiSIS API service is reachable and responsive.
     */
    public function isReachable(): bool
    {
        try {
            $headers = $this->getHeaders();
            $res = Http::timeout(2)->connectTimeout(1)->acceptJson();
            if (!empty($headers)) {
                $res = $res->withHeaders($headers);
            }
            $response = $res->get($this->baseUrl);
            return $response->successful() || $response->status() === 200;
        } catch (\Exception $e) {
            return false;
        }
    }
}
