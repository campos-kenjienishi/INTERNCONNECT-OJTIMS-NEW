<?php

namespace App\Console\Commands;

use App\Services\DataRetentionService;
use Illuminate\Console\Command;

class PreviewDataRetention extends Command
{
    protected $signature = 'retention:preview {category : requirements, completion_records, student_profile, or moa} {subject_id : Student user ID or company ID}';

    protected $description = 'Preview retention eligibility without deleting any database records or files';

    public function handle(DataRetentionService $retention): int
    {
        if (! $retention->ready() || ! isset(DataRetentionService::CATEGORIES[$this->argument('category')])) {
            $this->error('Run the retention migration and choose a valid category.');

            return self::FAILURE;
        }
        $plan = $retention->preview($this->argument('category'), (int) $this->argument('subject_id'));
        $this->table(['Category', 'Eligible', 'Due on', 'Records', 'File locations'], [[
            $plan['category'], $plan['eligible'] ? 'Yes' : 'No', $plan['due_on'] ?? 'Not established', $plan['record_count'], count($plan['files']),
        ]]);
        $this->info($plan['reason']);
        $this->line('Preview only. No records or files were deleted.');

        return self::SUCCESS;
    }
}
