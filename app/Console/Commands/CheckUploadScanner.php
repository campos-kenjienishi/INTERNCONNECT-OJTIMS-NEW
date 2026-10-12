<?php

namespace App\Console\Commands;

use App\Services\Uploads\MalwareScanner;
use Illuminate\Console\Command;

class CheckUploadScanner extends Command
{
    protected $signature = 'uploads:scanner-status';

    protected $description = 'Check whether upload malware scanning is configured and reachable';

    public function handle(MalwareScanner $scanner): int
    {
        $status = $scanner->status();
        $required = (bool) config('upload_security.malware_scan.required');

        $this->table(
            ['Setting', 'Value'],
            [
                ['Required', $required ? 'yes' : 'no'],
                ['Status', $status->status],
                ['Details', $status->message],
            ]
        );

        if ($status->ready) {
            $this->info('Upload malware scanning is operational.');

            return self::SUCCESS;
        }

        if (! $required && $status->status === 'disabled') {
            $hostingScanner = trim((string) config('upload_security.hosting_scanner'));

            if ($hostingScanner !== '') {
                $this->warn("Synchronous scanning is disabled. Uploads rely on {$hostingScanner} hosting-level protection and application validation.");
            } else {
                $this->warn('Uploads are allowed without malware scanning. Use this only for local development.');
            }

            return self::SUCCESS;
        }

        $this->error('Uploads are blocked until the malware scanner is operational.');

        return self::FAILURE;
    }
}
