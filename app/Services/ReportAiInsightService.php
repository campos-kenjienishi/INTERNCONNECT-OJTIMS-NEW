<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\GeminiClient;
use App\Services\OpenAiClient;

class ReportAiInsightService
{
    /**
     * Generate an AI summary and 3-pillar breakdown for a given report.
     */
    public function summarize(string $reportType, array $metrics, array $highlights = [], array $watchouts = [], array $actions = [], bool $forceAi = false): array
    {
        $sanitizedMetrics = $this->sanitizeReportMetrics($metrics);
        $provider = strtolower((string) config('services.ai.provider', 'gemini'));
        $model = (string) config('services.ai.model', 'gemini-2.5-flash');
        $ttl = (int) config('services.ai.cache_ttl', 300);

        if ($forceAi) {
            $ai = $this->tryConfiguredAi($reportType, $sanitizedMetrics, $highlights, $watchouts, $actions);
            if ($ai !== null) {
                return $ai;
            }

            return [
                'summary' => $this->buildFallbackSummary($reportType, $sanitizedMetrics),
                'key_findings' => $this->normalizeList(!empty($highlights) ? $highlights : $this->buildFallbackFindings($reportType, $sanitizedMetrics)),
                'watchouts' => $this->normalizeList(!empty($watchouts) ? $watchouts : $this->buildFallbackWatchouts($reportType, $sanitizedMetrics)),
                'recommendations' => $this->normalizeList(!empty($actions) ? $actions : $this->buildFallbackActions($reportType, $sanitizedMetrics)),
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
            'metrics' => $sanitizedMetrics,
            'highlights' => $highlights,
            'watchouts' => $watchouts,
            'actions' => $actions,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        return Cache::remember($cacheKey, $ttl, function () use ($reportType, $sanitizedMetrics, $highlights, $watchouts, $actions) {
            if ((bool) config('services.ai.auto_insights', false)) {
                $ai = $this->tryConfiguredAi($reportType, $sanitizedMetrics, $highlights, $watchouts, $actions);
                if ($ai !== null) {
                    return $ai;
                }
            }

            return [
                'summary' => $this->buildFallbackSummary($reportType, $sanitizedMetrics),
                'key_findings' => $this->normalizeList(!empty($highlights) ? $highlights : $this->buildFallbackFindings($reportType, $sanitizedMetrics)),
                'watchouts' => $this->normalizeList(!empty($watchouts) ? $watchouts : $this->buildFallbackWatchouts($reportType, $sanitizedMetrics)),
                'recommendations' => $this->normalizeList(!empty($actions) ? $actions : $this->buildFallbackActions($reportType, $sanitizedMetrics)),
                'source' => 'manual',
                'model' => null,
                'used_local_ai' => false,
                'ai_deferred' => true,
                'availability' => $this->providerAvailability(),
            ];
        });
    }

    /**
     * Answer a user question regarding report analytics with strict confidentiality guardrails.
     */
    public function answerQuestion(string $question, string $reportType, array $metrics, array $insight = []): array
    {
        $cleanQuestion = $this->sanitizeQuestion($question);
        $sanitizedMetrics = $this->sanitizeReportMetrics($metrics);

        if ($cleanQuestion === '') {
            return $this->buildFallbackAnswer('What should I focus on?', $sanitizedMetrics);
        }

        $prompt = $this->buildQuestionPrompt($cleanQuestion, $reportType, $sanitizedMetrics, $insight);
        $cacheKey = 'ai:ask:' . md5(strtolower((string) config('services.ai.provider', 'gemini')) . '|' . $prompt);
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

        return $this->buildFallbackAnswer($cleanQuestion, $sanitizedMetrics);
    }

    /**
     * Sanitize and strip confidential student PII and partner company private contact data.
     */
    public function sanitizeReportMetrics(array $metrics): array
    {
        $allowedKeys = [
            'report_type',
            'course',
            'class',
            'school_year',
            'semester',
            'total_students',
            'total_classes',
            'total_moa',
            'active_moa',
            'expired_moa',
            'expiring_soon_moa',
            'total_records',
            'total_companies',
            'records_with_ojt',
            'missing_ojt',
            'complete_students',
            'incomplete_students',
            'average_completion',
            'missing_requirements',
            'pending_requirements',
            'approved_requirements',
            'denied_requirements',
            'submitted_evaluations',
            'pending_evaluations',
            'expired_requests',
            'classes_with_pending',
            'required_categories',
            'modality_counts',
            'program_breakdown',
            'status_breakdown',
        ];

        $sanitized = [];
        foreach ($metrics as $key => $val) {
            if (in_array($key, $allowedKeys, true)) {
                if (is_string($val)) {
                    $sanitized[$key] = substr(trim(strip_tags($val)), 0, 100);
                } elseif (is_numeric($val) || is_bool($val)) {
                    $sanitized[$key] = $val;
                } elseif (is_array($val)) {
                    $sanitized[$key] = array_slice($val, 0, 30);
                }
            }
        }

        return $sanitized;
    }

    /**
     * Sanitize user questions to redact student numbers, email addresses, and phone numbers.
     */
    public function sanitizeQuestion(string $question): string
    {
        $question = trim($question);
        if ($question === '') {
            return '';
        }

        // Redact student numbers (e.g. 2022-00123-TG-0)
        $question = preg_replace('/\b\d{4}-\d{5}-[A-Z]{2}-\d\b/i', '[REDACTED_STUDENT_NUMBER]', $question);
        // Redact email addresses
        $question = preg_replace('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', '[REDACTED_EMAIL]', $question);
        // Redact phone numbers
        $question = preg_replace('/(\+?63|0)9\d{9}/', '[REDACTED_PHONE]', $question);

        return substr($question, 0, 500);
    }

    /**
     * Build system prompt for report insight generation.
     */
    protected function buildPrompt(string $reportType, array $metrics, array $highlights, array $watchouts, array $actions): string
    {
        $payload = [
            'report_type' => $reportType,
            'metrics' => $metrics,
            'highlights' => $highlights,
            'watchouts' => $watchouts,
            'actions' => $actions,
        ];

        return <<<PROMPT
You are "Bud", the OJT Intelligence Assistant for PUP Taguig InternConnect (On-the-Job Training Information Management System).
Analyze the provided report metrics for academic coordinators and faculty advisers.

STRICT PRIVACY & NON-DISCLOSURE POLICY:
- Never disclose, guess, or request individual student personal identifiable information (PII) such as student ID numbers, student full names, emails, phone numbers, or company contact person details.
- Rely exclusively on high-level statistical aggregates, program-level trends, compliance ratios, and institutional OJT workflow guidance.

INSTRUCTIONS:
1. Provide a professional, concise executive summary (2-3 sentences).
2. Detail 2-4 key findings highlighting positive compliance or institutional progress.
3. Detail 1-3 critical watchouts (e.g., expired MOAs, missing document bottlenecks, pending evaluations).
4. Detail 2-4 actionable recommendations for coordinators and advisers.

RESPONSE FORMAT (MUST BE VALID RAW JSON ONLY, NO MARKDOWN FENCES):
{
  "summary": "2-3 concise sentences summarizing key metrics and overall status.",
  "key_findings": ["Finding 1 with numbers/percentages", "Finding 2"],
  "watchouts": ["Watchout 1 with actionable alert", "Watchout 2"],
  "recommendations": ["Recommendation 1", "Recommendation 2", "Recommendation 3"]
}

INPUT DATA:
PROMPT
        . json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Build prompt for answering user questions about the report.
     */
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

        return <<<PROMPT
You are "Bud", the OJT Intelligence Assistant for PUP Taguig InternConnect.
Answer the user's question regarding this OJT report data accurately and concisely.

STRICT PRIVACY & NON-DISCLOSURE POLICY:
- Do not output student PII (student numbers, names, emails, phones) or company confidential contact info.
- Do not invent statistics, company names, or dates not present in the input.
- If the question asks for confidential student data or information outside the report, politely explain that you can only provide aggregate report analytics and offer a relevant statistical insight.

RESPONSE FORMAT (MUST BE VALID RAW JSON ONLY, NO MARKDOWN FENCES):
{
  "answer": "3-4 concise sentences answering the user query using only verified report metrics.",
  "next_steps": ["Actionable step 1", "Actionable step 2", "Actionable step 3"]
}

INPUT DATA:
PROMPT
        . json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
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
                . $completeStudents . ' student' . ($completeStudents === 1 ? '' : 's') . ' have completed all requirements (' . $averageCompletion . '% avg completion), while '
                . $missingRequirements . ' requirement item' . ($missingRequirements === 1 ? '' : 's') . ' remain missing.';
        }

        if (isset($metrics['active_moa']) || isset($metrics['expired_moa']) || $reportType === 'moa_report' || $reportType === 'moa') {
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
            . ' across ' . $companies . ' host compan' . ($companies === 1 ? 'y' : 'ies') . '.';

        if ($withOjt > 0) {
            $summary .= ' ' . $withOjt . ' student' . ($withOjt === 1 ? '' : 's') . ' have complete OJT placements recorded.';
        }

        if ($missingOjt > 0) {
            $summary .= ' ' . $missingOjt . ' student' . ($missingOjt === 1 ? '' : 's') . ' require placement verification.';
        }

        return $summary;
    }

    protected function buildFallbackFindings(string $reportType, array $metrics): array
    {
        $findings = [];

        if (isset($metrics['active_moa'])) {
            $active = (int) ($metrics['active_moa'] ?? 0);
            $total = (int) ($metrics['total_moa'] ?? 1);
            $rate = $total > 0 ? round(($active / $total) * 100) : 0;
            $findings[] = $active . ' partner companies have active, verified MOA agreements (' . $rate . '% coverage).';
            if (!empty($metrics['course'])) {
                $findings[] = 'Coverage analyzed specifically for the ' . $metrics['course'] . ' program.';
            }
        } elseif (isset($metrics['complete_students'])) {
            $complete = (int) ($metrics['complete_students'] ?? 0);
            $total = (int) ($metrics['total_students'] ?? 1);
            $rate = $total > 0 ? round(($complete / $total) * 100) : 0;
            $findings[] = $complete . ' out of ' . $total . ' students (' . $rate . '%) have fully submitted all required documents.';
            if (isset($metrics['average_completion'])) {
                $findings[] = 'Cohort overall average requirement progress is at ' . (int) $metrics['average_completion'] . '%.';
            }
        } elseif (isset($metrics['submitted_evaluations'])) {
            $findings[] = (int) $metrics['submitted_evaluations'] . ' supervisor performance evaluations completed.';
            if (isset($metrics['total_classes'])) {
                $findings[] = 'Evaluation tracking spanning across ' . (int) $metrics['total_classes'] . ' active sections.';
            }
        } else {
            $findings[] = 'Dataset contains ' . (int) ($metrics['total_records'] ?? $metrics['total_students'] ?? 0) . ' active student records.';
        }

        return $findings;
    }

    protected function buildFallbackWatchouts(string $reportType, array $metrics): array
    {
        $watchouts = [];

        if (isset($metrics['expired_moa']) && (int) $metrics['expired_moa'] > 0) {
            $watchouts[] = (int) $metrics['expired_moa'] . ' company MOA(s) have expired and require renewal notices.';
        }

        if (isset($metrics['missing_requirements']) && (int) $metrics['missing_requirements'] > 0) {
            $watchouts[] = (int) $metrics['missing_requirements'] . ' requirement item(s) remain unsubmitted by students.';
        }

        if (isset($metrics['denied_requirements']) && (int) $metrics['denied_requirements'] > 0) {
            $watchouts[] = (int) $metrics['denied_requirements'] . ' submission(s) were denied and require student re-upload.';
        }

        if (isset($metrics['pending_evaluations']) && (int) $metrics['pending_evaluations'] > 0) {
            $watchouts[] = (int) $metrics['pending_evaluations'] . ' evaluation link(s) pending supervisor completion.';
        }

        if (isset($metrics['missing_ojt']) && (int) $metrics['missing_ojt'] > 0) {
            $watchouts[] = (int) $metrics['missing_ojt'] . ' student(s) still need OJT placement details encoded.';
        }

        if (empty($watchouts)) {
            $watchouts[] = 'No critical compliance bottlenecks or expired agreements detected in this report.';
        }

        return $watchouts;
    }

    protected function buildFallbackActions(string $reportType, array $metrics): array
    {
        $actions = [];

        if (isset($metrics['expired_moa']) && (int) $metrics['expired_moa'] > 0) {
            $actions[] = 'Send automated renewal email notices to companies with expired agreements.';
            $actions[] = 'Review industry partnerships for upcoming semester placement expansions.';
        }

        if (isset($metrics['missing_requirements']) && (int) $metrics['missing_requirements'] > 0) {
            $actions[] = 'Send broadcast reminder to students with incomplete basic requirements.';
        }

        if (isset($metrics['denied_requirements']) && (int) $metrics['denied_requirements'] > 0) {
            $actions[] = 'Remind students with denied documents to review adviser feedback remarks and re-upload.';
        }

        if (isset($metrics['pending_evaluations']) && (int) $metrics['pending_evaluations'] > 0) {
            $actions[] = 'Resend digital evaluation links to pending company supervisors.';
        }

        if (empty($actions)) {
            $actions[] = 'Maintain current compliance review schedule.';
            $actions[] = 'Export or print updated report for institutional records.';
        }

        return $actions;
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
                'answer' => 'Based on the evaluation status, ' . $submitted . ' evaluations are submitted and ' . $pending . ' remain pending across ' . $classesWithPending . ' class section(s). ' . ($expired > 0 ? $expired . ' link requests have expired and may need resending.' : 'All active links are within valid timeframes.'),
                'next_steps' => array_values(array_filter([
                    $pending > 0 ? 'Review class sections with pending evaluations to identify follow-ups.' : null,
                    $expired > 0 ? 'Reissue evaluation links to supervisors with expired requests.' : null,
                    'Export evaluation summary once all scores are submitted.',
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
                'answer' => 'According to requirement status data, ' . $completeStudents . ' out of ' . $totalStudents . ' students have completed all categories. There are ' . $missingRequirements . ' missing items, ' . $pendingRequirements . ' pending reviews, and ' . $deniedRequirements . ' denied submissions requiring revision.',
                'next_steps' => array_values(array_filter([
                    $missingRequirements > 0 ? 'Check the Missing tab to identify students who need submission reminders.' : null,
                    $pendingRequirements > 0 ? 'Review and verify pending student uploads.' : null,
                    $deniedRequirements > 0 ? 'Advise students with denied submissions to re-upload corrected documents.' : null,
                    'Export or print the final compliance report.',
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
            $answer = 'Based on the MOA report' . $courseText . ', there are ' . $total . ' partner company records with '
                . $active . ' active agreement' . ($active === 1 ? '' : 's') . ' and '
                . $expired . ' expired agreement' . ($expired === 1 ? '' : 's') . '. ';

            if ($expired > $active) {
                $answer .= 'Renewal follow-up is the immediate priority since expired MOAs outnumber active partnerships.';
            } elseif ($expired > 0) {
                $answer .= 'Active partnership coverage is solid, but expired agreements should be renewed soon.';
            } else {
                $answer .= 'All partner companies in this cohort currently have active and verified agreements.';
            }

            return [
                'answer' => $answer,
                'next_steps' => array_values(array_filter([
                    $expired > 0 ? 'Send renewal email notices to the ' . $expired . ' partner company(ies).' : null,
                    'Review active partner company placements by academic program.',
                    'Use the filtered view before exporting or printing the report.',
                ])),
                'source' => 'fallback',
                'model' => null,
                'used_local_ai' => false,
                'availability' => $this->providerAvailability(),
            ];
        }

        return [
            'answer' => 'Based on the current report data, use the key trends, watchouts, and recommendations shown in the insight card to guide your next administrative actions.',
            'next_steps' => [
                'Review records with missing or incomplete information.',
                'Use the report filters to narrow the affected group.',
                'Export the finalized report for documentation.',
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

        // Remove markdown code fences if present
        if (preg_match('/```(?:json)?\s*(\{.*?\})\s*```/s', $trimmed, $matches)) {
            $trimmed = $matches[1];
        }

        $decoded = json_decode($trimmed, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // Extract between first '{' and last '}'
        $start = strpos($trimmed, '{');
        $end = strrpos($trimmed, '}');
        if ($start !== false && $end !== false && $end > $start) {
            $slice = substr($trimmed, $start, $end - $start + 1);
            $decoded = json_decode($slice, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }

            // Remove trailing commas before closing braces/brackets
            $cleaned = preg_replace('/,\s*([\}\]])/', '$1', $slice);
            $decoded = json_decode($cleaned, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    protected function tryGeminiAi(string $reportType, array $metrics, array $highlights, array $watchouts, array $actions): ?array
    {
        $model = (string) config('services.ai.model', 'gemini-2.5-flash') ?: 'gemini-2.5-flash';
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
            $model = (string) config('services.ai.model', 'gemini-2.5-flash') ?: 'gemini-2.5-flash';
            $raw = (new GeminiClient())->generate($prompt, $model);
            return $raw === null ? null : ['text' => $raw, 'source' => 'gemini', 'model' => $model];
        }

        return null;
    }
}
