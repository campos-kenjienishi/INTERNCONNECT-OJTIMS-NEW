<?php

namespace App\Console\Commands;

use App\Models\UploadSecurityRecord;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Throwable;

class VerifyUploadSecurity extends Command
{
    protected $signature = 'uploads:verify-security';

    protected $description = 'Verify active private uploads against their recorded SHA-256 checksums';

    public function handle(): int
    {
        if (! Schema::hasTable('upload_security_records')) {
            $this->error('The upload security records table is unavailable. Run database migrations first.');

            return self::FAILURE;
        }

        $records = UploadSecurityRecord::whereIn('lifecycle_status', ['active', 'missing', 'integrity_failed'])
            ->orderBy('id')
            ->get();
        $summary = [
            'verified' => 0,
            'missing' => 0,
            'integrity_failed' => 0,
            'unreadable' => 0,
        ];

        $this->withProgressBar($records, function (UploadSecurityRecord $record) use (&$summary): void {
            try {
                $disk = Storage::disk($record->storage_disk);

                if (! $disk->exists($record->storage_path)) {
                    $record->update([
                        'lifecycle_status' => 'missing',
                        'last_verified_at' => now(),
                    ]);
                    $summary['missing']++;

                    return;
                }

                $stream = $disk->readStream($record->storage_path);

                if ($stream === false) {
                    $this->markUnreadable($record, $summary);

                    return;
                }

                try {
                    $context = hash_init('sha256');
                    hash_update_stream($context, $stream);
                    $sha256 = hash_final($context);
                } finally {
                    fclose($stream);
                }

                $matches = hash_equals($record->sha256, $sha256);
                $record->update([
                    'lifecycle_status' => $matches ? 'active' : 'integrity_failed',
                    'last_verified_at' => now(),
                ]);
                $summary[$matches ? 'verified' : 'integrity_failed']++;
            } catch (Throwable) {
                $this->markUnreadable($record, $summary);
            }
        });

        $this->newLine(2);
        $this->table(
            ['Status', 'Count'],
            collect($summary)->map(fn ($count, $status) => [$status, $count])->values()->all()
        );

        if ($summary['missing'] > 0 || $summary['integrity_failed'] > 0 || $summary['unreadable'] > 0) {
            $this->error('Upload integrity issues were detected. No files were modified or deleted.');

            return self::FAILURE;
        }

        $this->info('All recorded active uploads passed integrity verification.');

        return self::SUCCESS;
    }

    private function markUnreadable(UploadSecurityRecord $record, array &$summary): void
    {
        $record->update([
            'lifecycle_status' => 'unreadable',
            'last_verified_at' => now(),
        ]);
        $summary['unreadable']++;
    }
}
