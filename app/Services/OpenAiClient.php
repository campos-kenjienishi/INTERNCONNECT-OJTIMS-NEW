<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiClient
{
    public function generate(string $prompt, ?string $model = null): ?string
    {
        $apiKey = (string) config('services.ai.openai_api_key', '');
        if ($apiKey === '') {
            return null;
        }

        $model = $model ?: (string) config('services.ai.openai_model', 'gpt-4.1-mini');
        $endpoint = (string) config('services.ai.openai_endpoint', 'https://api.openai.com/v1/responses');

        try {
            $response = Http::timeout(20)
                ->acceptJson()
                ->withToken($apiKey)
                ->post($endpoint, [
                    'model' => $model,
                    'input' => $prompt,
                    'max_output_tokens' => 800,
                ]);

            if (!$response->successful()) {
                Log::warning('OpenAiClient request failed', ['status' => $response->status(), 'body' => $response->body()]);
                return null;
            }

            $json = $response->json();

            if (isset($json['output_text'])) {
                return (string) $json['output_text'];
            }

            foreach (($json['output'] ?? []) as $output) {
                foreach (($output['content'] ?? []) as $content) {
                    if (isset($content['text'])) {
                        return (string) $content['text'];
                    }
                }
            }

            return $response->body();
        } catch (\Throwable $e) {
            Log::warning('OpenAiClient exception', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
