<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacultyApiService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.flss.base_url', 'https://flss.alquatrilixbsit2027.com'), '/');
        $this->apiKey = config('services.flss.api_key', '');
    }

    /**
     * Generate the three HMAC headers required by FLSS API.
     *
     * Message format: {method}|{url}|{body}|{timestamp}|{nonce}
     * Signed with HMAC-SHA256 using the API key.
     */
    protected function generateHmacHeaders(string $method, string $url, string $body = ''): array
    {
        $timestamp = (string) time();
        $nonce = '';

        $message = implode('|', [
            strtoupper($method),
            $url,
            $body,
            $timestamp,
            $nonce,
        ]);

        $signature = hash_hmac('sha256', $message, $this->apiKey);

        return [
            'X-HMAC-Signature' => $signature,
            'X-HMAC-Timestamp' => $timestamp,
            'X-HMAC-Nonce'     => $nonce,
        ];
    }

    /**
     * Retrieve the full list of active faculty members.
     * Endpoint: GET /api/v1/faculties
     *
     * @return array|null  Array with 'system' and 'faculties' keys, or null on failure.
     */
    public function getFacultyList(): ?array
    {
        if (empty($this->apiKey)) {
            Log::warning('FLSS API key is not configured.');
            return null;
        }

        $url = $this->baseUrl . '/api/v1/faculties';

        try {
            $headers = $this->generateHmacHeaders('GET', $url);

            $response = Http::timeout(30)
                ->retry(2, 500)
                ->acceptJson()
                ->withHeaders($headers)
                ->get($url);

            if ($response->successful()) {
                $data = $response->json();
                return is_array($data) ? $data : null;
            }

            Log::warning('FLSS API faculties returned non-200', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('FLSS API faculties exception', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Health check endpoint.
     * Endpoint: GET /api/health
     *
     * @return array|null  Array with health status, or null on failure.
     */
    public function healthCheck(): ?array
    {
        $url = $this->baseUrl . '/api/health';

        try {
            $headers = $this->generateHmacHeaders('GET', $url);

            $response = Http::timeout(5)
                ->acceptJson()
                ->withHeaders($headers)
                ->get($url);

            return $response->json();

        } catch (\Exception $e) {
            Log::error('FLSS API health check exception', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
