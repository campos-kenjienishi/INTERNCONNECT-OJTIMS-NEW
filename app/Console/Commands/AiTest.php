<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ReportAiInsightService;

class AiTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a quick AI summarization test using configured provider';

    public function handle()
    {
        $this->info('AI Provider: ' . config('services.ai.provider'));

        $provider = (string) config('services.ai.provider');

        if ($provider === 'openai' && empty(config('services.ai.openai_api_key'))) {
            $this->warn('OpenAI API key is not configured. The command will run fallback summarizer.');
        } elseif ($provider === 'openai') {
            $this->line('OpenAI endpoint: ' . config('services.ai.openai_endpoint'));
            $this->line('OpenAI model: ' . config('services.ai.openai_model'));
        }

        $endpoint = config('services.ai.gemini_endpoint');
        $key = config('services.ai.gemini_api_key');

        if ($provider === 'gemini' && empty($key)) {
            $this->warn('Gemini API key is not configured. The command will run fallback summarizer.');
        } elseif ($provider === 'gemini' && empty($endpoint)) {
            $this->line('Gemini endpoint is blank; using the default generateContent endpoint for the configured model.');
        }

        /** @var ReportAiInsightService $service */
        $service = app(ReportAiInsightService::class);

        $metrics = [
            'total_moa' => 10,
            'active_moa' => 8,
            'expired_moa' => 2,
            'course' => 'BSIT',
        ];

        $result = $service->summarize('moa', $metrics, ['Strong company engagement'], ['2 expired MOAs'], ['Follow up with partners'], true);

        $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return 0;
    }
}
