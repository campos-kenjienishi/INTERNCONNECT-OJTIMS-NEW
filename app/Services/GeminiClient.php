<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiClient
{
    public function generate(string $prompt, ?string $model = null): ?string
    {
        $endpoint = rtrim((string) config('services.ai.gemini_endpoint', ''), '/');
        if ($endpoint === '') {
            return null;
        }

        $apiKey = (string) config('services.ai.gemini_api_key', '');

        try {
            $client = Http::timeout(20)->acceptJson();
            if ($apiKey !== '') {
                $client = $client->withToken($apiKey);
            }

            $payload = array_filter([
                'model' => $model,
                'prompt' => $prompt,
            ]);

            $response = $client->post($endpoint, $payload);

            if (!$response->successful()) {
                Log::warning('GeminiClient request failed', ['status' => $response->status(), 'body' => $response->body()]);
                return null;
            }

            $json = $response->json();

            if (isset($json['candidates'][0]['content'])) {
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
            Log::warning('GeminiClient exception', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
