<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\GeminiClient;
use App\Services\OpenAiClient;

class ReportAiInsightService
{
    public function summarize(string $reportType, array $metrics, array $highlights = [], array $watchouts = [], array $actions = [], bool $forceAi = false): array
    {
        $provider = strtolower((string) config('services.ai.provider', 'fallback'));
        $model = (string) config('services.ai.model', '');
        $ttl = (int) config('services.ai.cache_ttl', 300);

        if ($forceAi) {
            $ai = $this->tryConfiguredAi($reportType, $metrics, $highlights, $watchouts, $actions);
            if ($ai !== null) {
                return $ai;
            }

            return [
                'summary' => $this->buildFallbackSummary($reportType, $metrics),
                'key_findings' => $this->normalizeList($highlights),
                'watchouts' => $this->normalizeList($watchouts),
                'recommendations' => $this->normalizeList($actions),
                'source' => 'fallback',
                'model' => null,
                'used_local_ai' => false,
                'ai_deferred' => false,
                'availability' => $this->providerAvailability(),
            ];
        }

        $cacheKey = 'ai:summary:' . md5(json_encode([
            'provider' => $provider,
            'model' => $model,
            'report_type' => $reportType,
            'mode' => (bool) config('services.ai.auto_insights', false) ? 'ai' : 'manual',
            'metrics' => $metrics,
            'highlights' => $highlights,
            'watchouts' => $watchouts,
            'actions' => $actions,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        return Cache::remember($cacheKey, $ttl, function () use ($reportType, $metrics, $highlights, $watchouts, $actions) {
            if ((bool) config('services.ai.auto_insights', false)) {
                $ai = $this->tryConfiguredAi($reportType, $metrics, $highlights, $watchouts, $actions);
                if ($ai !== null) {
                    return $ai;
                }
            }

            return [
                'summary' => $this->buildFallbackSummary($reportType, $metrics),
                'key_findings' => $this->normalizeList($highlights),
                'watchouts' => $this->normalizeList($watchouts),
                'recommendations' => $this->normalizeList($actions),
                'source' => 'manual',
                'model' => null,
                'used_local_ai' => false,
                'ai_deferred' => true,
                'availability' => $this->providerAvailability(),
            ];
        });
    }

    public function answerQuestion(string $question, string $reportType, array $metrics, array $insight = []): array
    {
        $question = trim($question);
        if ($question === '') {
            return $this->buildFallbackAnswer('What should I focus on?', $metrics);
        }

        $prompt = $this->buildQuestionPrompt($question, $reportType, $metrics, $insight);
        $cacheKey = 'ai:ask:' . md5(strtolower((string) config('services.ai.provider', 'fallback')) . '|' . $prompt);
        $ttl = (int) config('services.ai.cache_ttl', 300);

        $answer = Cache::remember($cacheKey, $ttl, function () use ($prompt) {
            $generated = $this->generateConfiguredText($prompt);
            if ($generated === null) {
                return null;
            }

            $decoded = $this->extractJsonObject($generated['text']);
            if ($decoded === null) {
                Log::warning('AI question response could not be parsed as JSON', ['snippet' => substr($generated['text'], 0, 500)]);
                return null;
            }

            return [
                'answer' => trim((string) ($decoded['answer'] ?? '')),
                'next_steps' => $this->normalizeList($decoded['next_steps'] ?? $decoded['actions'] ?? []),
                'source' => $generated['source'],
                'model' => $generated['model'],
                'used_local_ai' => false,
            ];
        });

        if ($answer !== null && $answer['answer'] !== '') {
            return $answer;
        }

        return $this->buildFallbackAnswer($question, $metrics);
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

    protected function buildQuestionPrompt(string $question, string $reportType, array $metrics, array $insight): string
    {
        $payload = [
            'question' => $question,
            'report_type' => $reportType,
            'metrics' => $metrics,
            'current_insight' => [
                'summary' => $insight['summary'] ?? null,
                'key_findings' => $insight['key_findings'] ?? [],
                'watchouts' => $insight['watchouts'] ?? [],
                'recommendations' => $insight['recommendations'] ?? [],
            ],
        ];

        return 'You are an assistant inside a school OJT information management system. '
            . 'Answer the user question using only the report data provided. '
            . 'Do not invent company names, student names, dates, or counts that are not present. '
            . 'If the question is outside the report data, briefly say you can only answer using the current report and suggest a relevant report question. '
            . 'Return only valid JSON with these keys: answer, next_steps. '
            . 'Keep answer to 3-5 concise sentences and next_steps to 2-4 practical bullets. '
            . 'Input data: ' . json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    protected function buildFallbackSummary(string $reportType, array $metrics): string
    {
        if ($reportType === 'professor_evaluation') {
            $classes = (int) ($metrics['total_classes'] ?? 0);
            $students = (int) ($metrics['total_students'] ?? 0);
            $submitted = (int) ($metrics['submitted_evaluations'] ?? 0);
            $pending = (int) ($metrics['pending_evaluations'] ?? 0);
            $expired = (int) ($metrics['expired_requests'] ?? 0);

            return 'The evaluation status covers ' . $classes . ' class' . ($classes === 1 ? '' : 'es') . ' and ' . $students . ' student' . ($students === 1 ? '' : 's')
                . '. ' . $submitted . ' evaluation' . ($submitted === 1 ? '' : 's') . ' have been submitted, '
                . $pending . ' remain pending, and ' . $expired . ' request' . ($expired === 1 ? '' : 's') . ' have expired.';
        }

        if ($reportType === 'requirement_status') {
            $totalStudents = (int) ($metrics['total_students'] ?? 0);
            $categoryCount = (int) ($metrics['required_categories'] ?? 0);
            $completeStudents = (int) ($metrics['complete_students'] ?? 0);
            $averageCompletion = (int) ($metrics['average_completion'] ?? 0);
            $missingRequirements = (int) ($metrics['missing_requirements'] ?? 0);
            $class = trim((string) ($metrics['class'] ?? ''));
            $classSuffix = $class !== '' ? ' for ' . $class : '';

            return 'The requirement status report' . $classSuffix . ' covers ' . $totalStudents . ' student' . ($totalStudents === 1 ? '' : 's')
                . ' across ' . $categoryCount . ' required categor' . ($categoryCount === 1 ? 'y' : 'ies') . '. '
                . $completeStudents . ' student' . ($completeStudents === 1 ? '' : 's') . ' are complete, average completion is ' . $averageCompletion . '%, and '
                . $missingRequirements . ' requirement item' . ($missingRequirements === 1 ? '' : 's') . ' are still missing.';
        }

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

    protected function buildFallbackAnswer(string $question, array $metrics): array
    {
        if (isset($metrics['submitted_evaluations']) || isset($metrics['pending_evaluations'])) {
            $submitted = (int) ($metrics['submitted_evaluations'] ?? 0);
            $pending = (int) ($metrics['pending_evaluations'] ?? 0);
            $expired = (int) ($metrics['expired_requests'] ?? 0);
            $classesWithPending = (int) ($metrics['classes_with_pending'] ?? 0);

            return [
                'answer' => 'Based on the current evaluation status, ' . $submitted . ' evaluations have been submitted while ' . $pending . ' are still pending. ' . $classesWithPending . ' classes still need follow-up, and ' . $expired . ' requests are expired, so the next action should focus on pending and expired evaluation links.',
                'next_steps' => array_values(array_filter([
                    $pending > 0 ? 'Open classes with pending evaluations and identify students needing reminders.' : null,
                    $expired > 0 ? 'Reissue expired evaluation requests where appropriate.' : null,
                    'Review the active template before sending new evaluation links.',
                ])),
                'source' => 'fallback',
                'model' => null,
                'used_local_ai' => false,
                'availability' => $this->providerAvailability(),
            ];
        }

        if (isset($metrics['required_categories']) || isset($metrics['missing_requirements'])) {
            $totalStudents = (int) ($metrics['total_students'] ?? 0);
            $completeStudents = (int) ($metrics['complete_students'] ?? 0);
            $missingRequirements = (int) ($metrics['missing_requirements'] ?? 0);
            $pendingRequirements = (int) ($metrics['pending_requirements'] ?? 0);
            $deniedRequirements = (int) ($metrics['denied_requirements'] ?? 0);

            return [
                'answer' => 'Based on the current requirement status data, ' . $completeStudents . ' out of ' . $totalStudents . ' students have completed all tracked categories. There are ' . $missingRequirements . ' missing requirement items, ' . $pendingRequirements . ' pending submissions, and ' . $deniedRequirements . ' denied submissions, so follow-up should focus on missing, pending, and denied items first.',
                'next_steps' => array_values(array_filter([
                    $missingRequirements > 0 ? 'Use the Missing tab to identify students who still need to submit files.' : null,
                    $pendingRequirements > 0 ? 'Review pending submitted files for approval or denial.' : null,
                    $deniedRequirements > 0 ? 'Ask students with denied files to resubmit corrected documents.' : null,
                    'Export or print the report after the review status is updated.',
                ])),
                'source' => 'fallback',
                'model' => null,
                'used_local_ai' => false,
                'availability' => $this->providerAvailability(),
            ];
        }

        $total = (int) ($metrics['total_moa'] ?? $metrics['total_records'] ?? 0);
        $active = (int) ($metrics['active_moa'] ?? 0);
        $expired = (int) ($metrics['expired_moa'] ?? 0);
        $course = trim((string) ($metrics['course'] ?? ''));
        $courseText = $course !== '' ? ' for ' . $course : '';

        if (isset($metrics['total_moa'])) {
            $answer = 'Based on the current MOA report' . $courseText . ', there are ' . $total . ' partner company records, with '
                . $active . ' active agreement' . ($active === 1 ? '' : 's') . ' and '
                . $expired . ' expired agreement' . ($expired === 1 ? '' : 's') . '. ';

            if ($expired > $active) {
                $answer .= 'The expired MOA count is higher than the active count, so renewal follow-up should be treated as the main priority.';
            } elseif ($expired > 0) {
                $answer .= 'The report still needs renewal follow-up for the expired MOAs, but active coverage remains stronger than expired coverage.';
            } else {
                $answer .= 'No expired MOAs are currently detected in this report.';
            }

            return [
                'answer' => $answer,
                'next_steps' => array_values(array_filter([
                    $expired > 0 ? 'Prepare a renewal list for expired MOAs.' : null,
                    'Review active partner coverage by course.',
                    'Use the filtered report before exporting or printing.',
                ])),
                'source' => 'fallback',
                'model' => null,
                'used_local_ai' => false,
                'availability' => $this->providerAvailability(),
            ];
        }

        return [
            'answer' => 'Based on the current report data, use the totals, watchouts, and recommendations shown in the insight card to prioritize the next review actions.',
            'next_steps' => [
                'Review records with missing or incomplete information.',
                'Use the report filters to narrow the affected group.',
            ],
            'source' => 'fallback',
            'model' => null,
            'used_local_ai' => false,
            'availability' => $this->providerAvailability(),
        ];
    }

    protected function providerAvailability(): ?array
    {
        $provider = strtolower((string) config('services.ai.provider', 'gemini'));

        if ($provider === 'gemini') {
            return Cache::get('ai:gemini:last_unavailable');
        }

        return null;
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

    protected function tryOpenAi(string $reportType, array $metrics, array $highlights, array $watchouts, array $actions): ?array
    {
        $model = (string) config('services.ai.openai_model', '') ?: (string) config('services.ai.model', '') ?: null;

        $prompt = $this->buildPrompt($reportType, $metrics, $highlights, $watchouts, $actions);

        $cacheKey = 'ai:openai:' . md5($model . '|' . $prompt);
        $ttl = (int) config('services.ai.cache_ttl', 300);

        return Cache::remember($cacheKey, $ttl, function () use ($prompt, $model, $reportType, $metrics, $highlights, $watchouts, $actions) {
            $client = new OpenAiClient();
            $raw = $client->generate($prompt, $model);
            if ($raw === null) {
                return null;
            }

            $decoded = $this->extractJsonObject($raw);
            if ($decoded === null) {
                Log::warning('OpenAI response could not be parsed as JSON', ['snippet' => substr($raw, 0, 500)]);
                return null;
            }

            return [
                'summary' => trim((string) ($decoded['summary'] ?? $this->buildFallbackSummary($reportType, $metrics))),
                'key_findings' => $this->normalizeList($decoded['key_findings'] ?? $decoded['findings'] ?? $highlights),
                'watchouts' => $this->normalizeList($decoded['watchouts'] ?? $decoded['risks'] ?? $watchouts),
                'recommendations' => $this->normalizeList($decoded['recommendations'] ?? $decoded['actions'] ?? $actions),
                'source' => 'openai',
                'model' => $model,
                'used_local_ai' => false,
            ];
        });
    }

    protected function tryConfiguredAi(string $reportType, array $metrics, array $highlights, array $watchouts, array $actions): ?array
    {
        $provider = strtolower((string) config('services.ai.provider', 'gemini'));

        if ($provider === 'openai') {
            return $this->tryOpenAi($reportType, $metrics, $highlights, $watchouts, $actions);
        }

        if ($provider === 'gemini') {
            return $this->tryGeminiAi($reportType, $metrics, $highlights, $watchouts, $actions);
        }

        return null;
    }

    protected function generateConfiguredText(string $prompt): ?array
    {
        $provider = strtolower((string) config('services.ai.provider', 'gemini'));

        if ($provider === 'openai') {
            $model = (string) config('services.ai.openai_model', '') ?: (string) config('services.ai.model', '') ?: null;
            $raw = (new OpenAiClient())->generate($prompt, $model);
            return $raw === null ? null : ['text' => $raw, 'source' => 'openai', 'model' => $model];
        }

        if ($provider === 'gemini') {
            $model = (string) config('services.ai.model', '') ?: null;
            $raw = (new GeminiClient())->generate($prompt, $model);
            return $raw === null ? null : ['text' => $raw, 'source' => 'gemini', 'model' => $model];
        }

        return null;
    }
}
