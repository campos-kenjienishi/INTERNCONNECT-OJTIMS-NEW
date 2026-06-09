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

        $endpoint = config('services.ai.gemini_endpoint');
        $key = config('services.ai.gemini_api_key');

        if (empty($endpoint) || empty($key)) {
            $this->warn('Gemini endpoint or API key not configured. The command will run fallback summarizer.');
        }

        /** @var ReportAiInsightService $service */
        $service = app(ReportAiInsightService::class);

        $metrics = [
            'total_moa' => 10,
            'active_moa' => 8,
            'expired_moa' => 2,
            'course' => 'BSIT',
        ];

        $result = $service->summarize('moa', $metrics, ['Strong company engagement'], ['2 expired MOAs'], ['Follow up with partners']);

        $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return 0;
    }
}
