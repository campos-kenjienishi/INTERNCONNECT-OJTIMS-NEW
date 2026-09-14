<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GeminiClient
{
    public function generate(string $prompt, ?string $model = null): ?string
    {
        $apiKey = (string) config('services.ai.gemini_api_key', '');
        if ($apiKey === '') {
            return null;
        }

        $configuredModel = (string) config('services.ai.model', 'gemini-2.5-flash');
        $model = $model ?: ($configuredModel !== '' ? $configuredModel : 'gemini-2.5-flash');

        // Normalize non-existent or legacy model names
        if (str_contains($model, '3.5') || $model === '') {
            $model = 'gemini-2.5-flash';
        }

        $endpoint = trim((string) config('services.ai.gemini_endpoint', ''));

        if ($endpoint === '') {
            $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode($model) . ':generateContent';
        }

        try {
            $payload = [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'maxOutputTokens' => 2048,
                    'temperature' => 0.2,
                    'responseMimeType' => 'application/json',
                ],
            ];

            $response = Http::timeout(20)
                ->acceptJson()
                ->withHeaders(['x-goog-api-key' => $apiKey])
                ->post($endpoint, $payload);

            if (!$response->successful()) {
                $this->rememberUnavailableState($response->status(), $response->json(), $response->body());
                Log::warning('GeminiClient request failed', ['status' => $response->status(), 'body' => $response->body()]);
                return null;
            }

            // On success, clear any stale unavailable flag
            Cache::forget('ai:gemini:last_unavailable');

            $json = $response->json();

            if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
                return (string) $json['candidates'][0]['content']['parts'][0]['text'];
            }

            if (isset($json['candidates'][0]['content']) && is_string($json['candidates'][0]['content'])) {
                return (string) $json['candidates'][0]['content'];
            }

            if (isset($json['response'])) {
                return (string) $json['response'];
            }

            if (isset($json['output'][0]['content'])) {
                return (string) $json['output'][0]['content'];
            }

            return $response->body();
        } catch (\Throwable $e) {
            $this->rememberUnavailableState(null, null, $e->getMessage());
            Log::warning('GeminiClient exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    protected function rememberUnavailableState(?int $status, ?array $json, string $rawMessage): void
    {
        $retryAfterSeconds = $this->extractRetryDelaySeconds($json);
        $retryAt = $retryAfterSeconds !== null ? now()->addSeconds($retryAfterSeconds) : null;
        $isRateLimited = $status === 429 || str_contains(strtolower($rawMessage), 'quota') || str_contains(strtolower($rawMessage), 'resource_exhausted');

        $message = $isRateLimited
            ? 'Gemini quota limit reached.'
            : 'Gemini is temporarily busy.';

        if ($retryAt !== null) {
            $message .= ' Will resume around ' . $retryAt->format('g:i A') . '.';
        } elseif ($isRateLimited) {
            $message .= ' Automatic fallback insights active.';
        } else {
            $message .= ' Automatic fallback insights active.';
        }

        Cache::put('ai:gemini:last_unavailable', [
            'status' => $status,
            'message' => $message,
            'retry_after_seconds' => $retryAfterSeconds,
            'retry_at' => $retryAt?->toIso8601String(),
            'reason' => $isRateLimited ? 'rate_limit' : 'unavailable',
        ], now()->addMinutes(10));
    }

    protected function extractRetryDelaySeconds(?array $json): ?int
    {
        if (!is_array($json)) {
            return null;
        }

        foreach (($json['error']['details'] ?? []) as $detail) {
            $retryDelay = $detail['retryDelay'] ?? null;
            if (!is_string($retryDelay) || $retryDelay === '') {
                continue;
            }

            if (preg_match('/^(\d+)(?:\.(\d+))?s$/', $retryDelay, $matches)) {
                return max(1, (int) ceil((float) $matches[1] + (isset($matches[2]) ? (float) ('0.' . $matches[2]) : 0)));
            }

            if (preg_match('/^(\d+)(?:\.(\d+))?ms$/', $retryDelay, $matches)) {
                return 1;
            }
        }

        return null;
    }
}

