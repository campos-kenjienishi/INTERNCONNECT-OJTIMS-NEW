<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\GeminiClient;

class ReportAiInsightService
{
    public function summarize(string $reportType, array $metrics, array $highlights = [], array $watchouts = [], array $actions = []): array
    {
        // Use Gemini as primary provider; fall back to internal summary.
        $gemini = $this->tryGeminiAi($reportType, $metrics, $highlights, $watchouts, $actions);
        if ($gemini !== null) {
            return $gemini;
        }

        return [
            'summary' => $this->buildFallbackSummary($reportType, $metrics),
            'key_findings' => $this->normalizeList($highlights),
            'watchouts' => $this->normalizeList($watchouts),
            'recommendations' => $this->normalizeList($actions),
            'source' => 'fallback',
            'model' => null,
            'used_local_ai' => false,
        ];
    }

    

    protected function buildPrompt(string $reportType, array $metrics, array $highlights, array $watchouts, array $actions): string
    {
        $payload = [
            'report_type' => $reportType,
            'metrics' => $metrics,
            'highlights' => $highlights,
            'watchouts' => $watchouts,
            'actions' => $actions,
        ];

        return 'You are helping a school OJT system summarize report data for coordinators and professors. '
            . 'Return only valid JSON with these keys: summary, key_findings, watchouts, recommendations. '
            . 'Keep the summary to 2-3 short sentences and make the output concise and practical. '
            . 'Use the following input data: ' . json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    protected function buildFallbackSummary(string $reportType, array $metrics): string
    {
        if (isset($metrics['active_moa']) || isset($metrics['expired_moa'])) {
            $total = (int) ($metrics['total_moa'] ?? 0);
            $active = (int) ($metrics['active_moa'] ?? 0);
            $expired = (int) ($metrics['expired_moa'] ?? 0);
            $course = trim((string) ($metrics['course'] ?? ''));
            $courseSuffix = $course !== '' ? ' for ' . $course : '';

            return 'The MOA report' . $courseSuffix . ' shows ' . $total . ' partner company record' . ($total === 1 ? '' : 's')
                . ' with ' . $active . ' active and ' . $expired . ' expired agreement' . ($expired === 1 ? '' : 's') . '.';
        }

        $totalRecords = (int) ($metrics['total_records'] ?? 0);
        $companies = (int) ($metrics['total_companies'] ?? 0);
        $withOjt = (int) ($metrics['records_with_ojt'] ?? 0);
        $missingOjt = (int) ($metrics['missing_ojt'] ?? 0);
        $course = trim((string) ($metrics['course'] ?? ''));
        $courseSuffix = $course !== '' ? ' for ' . $course : '';

        $summary = 'The student OJT report' . $courseSuffix . ' covers ' . $totalRecords . ' student record' . ($totalRecords === 1 ? '' : 's')
            . ' across ' . $companies . ' compan' . ($companies === 1 ? 'y' : 'ies') . '.';

        if ($withOjt > 0) {
            $summary .= ' ' . $withOjt . ' record' . ($withOjt === 1 ? '' : 's') . ' have linked OJT details.';
        }

        if ($missingOjt > 0) {
            $summary .= ' ' . $missingOjt . ' record' . ($missingOjt === 1 ? '' : 's') . ' still need OJT verification.';
        }

        return $summary;
    }

    protected function normalizeList($items): array
    {
        if (is_string($items)) {
            $items = [$items];
        }

        if (!is_array($items)) {
            return [];
        }

        return array_values(array_filter(array_map(function ($item) {
            if (is_array($item)) {
                $item = reset($item);
            }

            $item = trim((string) $item);
            return $item !== '' ? $item : null;
        }, $items)));
    }

    protected function extractJsonObject(string $text): ?array
    {
        $trimmed = trim($text);

        if ($trimmed === '') {
            return null;
        }

        $decoded = json_decode($trimmed, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{.*\}/s', $trimmed, $matches)) {
            $decoded = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    protected function tryGeminiAi(string $reportType, array $metrics, array $highlights, array $watchouts, array $actions): ?array
    {
        $model = (string) config('services.ai.model', '') ?: null;

        $prompt = $this->buildPrompt($reportType, $metrics, $highlights, $watchouts, $actions);

        $cacheKey = 'ai:gemini:' . md5($model . '|' . $prompt);
        $ttl = (int) config('services.ai.cache_ttl', 300);

        return Cache::remember($cacheKey, $ttl, function () use ($prompt, $model, $reportType, $metrics, $highlights, $watchouts, $actions) {
            $client = new GeminiClient();
            $raw = $client->generate($prompt, $model);
            if ($raw === null) {
                return null;
            }

            $decoded = $this->extractJsonObject($raw);
            if ($decoded === null) {
                // store negative result short-term to avoid repeated parsing
                Log::warning('Gemini response could not be parsed as JSON', ['snippet' => substr($raw, 0, 500)]);
                return null;
            }

            return [
                'summary' => trim((string) ($decoded['summary'] ?? $this->buildFallbackSummary($reportType, $metrics))),
                'key_findings' => $this->normalizeList($decoded['key_findings'] ?? $decoded['findings'] ?? $highlights),
                'watchouts' => $this->normalizeList($decoded['watchouts'] ?? $decoded['risks'] ?? $watchouts),
                'recommendations' => $this->normalizeList($decoded['recommendations'] ?? $decoded['actions'] ?? $actions),
                'source' => 'gemini',
                'model' => $model,
                'used_local_ai' => false,
            ];
        });
    }
}