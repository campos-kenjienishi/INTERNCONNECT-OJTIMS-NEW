<?php

namespace App\Console\Commands;

use App\Services\LegacyDocumentMigrationService;
use App\Services\PrivateDocumentStorage;
use App\Services\Uploads\MalwareScanner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class MigrateLegacyUploads extends Command
{
    protected $signature = 'uploads:migrate-legacy
        {--execute : Copy validated files into private storage}
        {--allow-unscanned : Permit migration only when malware scanning is intentionally disabled}
        {--force : Permit execution in production}';

    protected $description = 'Copy referenced legacy uploads into checksum-verified private storage';

    public function handle(LegacyDocumentMigrationService $migrator, MalwareScanner $scanner): int
    {
        $execute = (bool) $this->option('execute');
        $allowUnscanned = (bool) $this->option('allow-unscanned');

        if ($execute && app()->isProduction() && ! $this->option('force')) {
            $this->error('Production execution requires both --execute and --force. Run a dry-run first.');

            return self::FAILURE;
        }

        $scannerStatus = $scanner->status();

        if ($execute && ! $scannerStatus->ready && ! ($allowUnscanned && $scannerStatus->status === 'disabled')) {
            $this->error('The malware scanner must be ready before migration. Use --allow-unscanned only when scanning is intentionally disabled.');

            return self::FAILURE;
        }

        $documents = $this->inventory();
        $orphanedSources = $this->orphanedSources($documents);
        $this->info(sprintf('%s %d unique referenced legacy document(s).', $execute ? 'Preflighting' : 'Inspecting', count($documents)));
        $results = $this->processDocuments($documents, $migrator, false, $allowUnscanned);
        $summary = $this->displaySummary($results);
        $mode = $execute ? 'preflight' : 'dry-run';
        $reportPath = $this->writeReport($mode, $scannerStatus->status, $summary, $results, $orphanedSources);
        $this->info('Audit report: '.$reportPath);

        if ($orphanedSources !== []) {
            $this->warn(count($orphanedSources).' unreferenced legacy file(s) were retained and listed in the report.');
        }

        if ($this->failureCount($summary) > 0) {
            $this->error('Migration stopped because the preflight found unresolved records. No source files were modified or deleted.');

            return self::FAILURE;
        }

        if (! $execute) {
            $this->warn('Legacy source files were not modified or deleted.');

            return self::SUCCESS;
        }

        if (! $this->hasSufficientDiskSpace($results)) {
            $this->error('Migration stopped because private storage does not have enough verified free space.');

            return self::FAILURE;
        }

        $this->info('Preflight passed. Copying validated files into private storage.');
        $results = $this->processDocuments($documents, $migrator, true, $allowUnscanned);
        $summary = $this->displaySummary($results);
        $reportPath = $this->writeReport('execute', $scannerStatus->status, $summary, $results, $orphanedSources);
        $this->info('Audit report: '.$reportPath);
        $this->warn('Legacy source files were not modified or deleted.');

        return $this->failureCount($summary) > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function processDocuments(
        array $documents,
        LegacyDocumentMigrationService $migrator,
        bool $execute,
        bool $allowUnscanned
    ): array {
        $results = [];

        $this->withProgressBar($documents, function (array $document) use (&$results, $migrator, $execute, $allowUnscanned): void {
            $result = $migrator->migrate(
                $document['directory'],
                $document['filename'],
                $document['profile'],
                $execute,
                $allowUnscanned
            );

            $results[] = array_merge($result->toArray(), ['references' => $document['references']]);
        });

        $this->newLine(2);

        return $results;
    }

    private function displaySummary(array $results): array
    {
        $summary = collect($results)->countBy('status')->sortKeys()->all();
        $this->table(['Status', 'Count'], collect($summary)->map(fn ($count, $status) => [$status, $count])->values()->all());

        return $summary;
    }

    private function failureCount(array $summary): int
    {
        return array_sum(array_intersect_key($summary, array_flip([
            'conflict',
            'copy_failed',
            'evidence_failed',
            'missing_source',
            'security_rejected',
            'unsafe_filename',
            'unsafe_source',
            'unsafe_destination',
            'unscanned',
            'verification_failed',
        ])));
    }

    private function hasSufficientDiskSpace(array $results): bool
    {
        $requiredBytes = (int) collect($results)
            ->where('status', 'pending')
            ->sum(fn (array $result) => (int) ($result['size'] ?? 0));

        if ($requiredBytes === 0) {
            return true;
        }

        $diskName = (string) config('upload_security.disk');
        $diskConfig = config("filesystems.disks.{$diskName}");

        if (! is_array($diskConfig) || ($diskConfig['driver'] ?? null) !== 'local' || empty($diskConfig['root'])) {
            $this->warn('Automatic free-space verification is unavailable for this storage driver. Confirm capacity with the provider before continuing.');

            return true;
        }

        $root = (string) $diskConfig['root'];
        File::ensureDirectoryExists($root);
        $freeBytes = disk_free_space($root);

        if ($freeBytes === false) {
            return false;
        }

        $headroomBytes = max(50 * 1024 * 1024, (int) ceil($requiredBytes * 0.10));
        $this->line(sprintf(
            'Storage preflight: %s required plus %s headroom; %s available.',
            $this->formatBytes($requiredBytes),
            $this->formatBytes($headroomBytes),
            $this->formatBytes((int) $freeBytes)
        ));

        return $freeBytes >= ($requiredBytes + $headroomBytes);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }

        $units = ['KB', 'MB', 'GB', 'TB'];
        $value = $bytes / 1024;

        foreach ($units as $unit) {
            if ($value < 1024 || $unit === 'TB') {
                return number_format($value, 2).' '.$unit;
            }

            $value /= 1024;
        }

        return $bytes.' B';
    }

    private function inventory(): array
    {
        $documents = [];

        $this->collectReferences(
            $documents,
            'uploaded_files',
            PrivateDocumentStorage::TEMPLATES,
            'template'
        );

        $this->collectRequirementReferences($documents);

        $this->collectReferences(
            $documents,
            'companies',
            PrivateDocumentStorage::MOA,
            'pdf'
        );

        ksort($documents);

        return array_values($documents);
    }

    private function collectReferences(array &$documents, string $table, string $directory, string $profile): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'file')) {
            return;
        }

        foreach (DB::table($table)->select('id', 'file')->whereNotNull('file')->orderBy('id')->cursor() as $record) {
            $this->addReference($documents, $directory, (string) $record->file, $profile, $table.':'.$record->id);
        }
    }

    private function collectRequirementReferences(array &$documents): void
    {
        if (! Schema::hasTable('file_requirements') || ! Schema::hasColumn('file_requirements', 'file')) {
            return;
        }

        $hasName = Schema::hasColumn('file_requirements', 'fileName');
        $columns = $hasName ? ['id', 'file', 'fileName'] : ['id', 'file'];

        foreach (DB::table('file_requirements')->select($columns)->whereNotNull('file')->orderBy('id')->cursor() as $record) {
            $isMoa = $hasName && trim((string) $record->fileName) === 'Notarized MOA';
            $this->addReference(
                $documents,
                $isMoa ? PrivateDocumentStorage::MOA : PrivateDocumentStorage::REQUIREMENTS,
                (string) $record->file,
                'pdf',
                'file_requirements:'.$record->id
            );
        }
    }

    private function addReference(
        array &$documents,
        string $directory,
        string $filename,
        string $profile,
        string $reference
    ): void {
        $key = $directory."\0".$filename;

        if (! isset($documents[$key])) {
            $documents[$key] = compact('directory', 'filename', 'profile');
            $documents[$key]['references'] = [];
        }

        $documents[$key]['references'][] = $reference;
    }

    private function orphanedSources(array $documents): array
    {
        $legacyDirectory = (string) config('upload_security.legacy_directory');

        if (! is_dir($legacyDirectory)) {
            return [];
        }

        $referencedNames = array_fill_keys(array_column($documents, 'filename'), true);
        $orphans = [];

        foreach (File::files($legacyDirectory) as $file) {
            $filename = $file->getFilename();

            if ($filename === '.htaccess' || isset($referencedNames[$filename])) {
                continue;
            }

            $sha256 = hash_file('sha256', $file->getPathname());
            $orphans[] = [
                'filename' => $filename,
                'size' => $file->getSize(),
                'sha256' => $sha256 === false ? null : $sha256,
            ];
        }

        return $orphans;
    }

    private function writeReport(
        string $mode,
        string $scannerStatus,
        array $summary,
        array $results,
        array $orphanedSources
    ): string {
        $directory = storage_path('app/migration-reports');
        File::ensureDirectoryExists($directory);
        $path = $directory.'/legacy-upload-migration-'.now()->format('Ymd-His-u').'-'.$mode.'.json';
        $payload = [
            'generated_at' => now()->toIso8601String(),
            'mode' => $mode,
            'legacy_directory' => (string) config('upload_security.legacy_directory'),
            'destination_disk' => (string) config('upload_security.disk'),
            'scanner_status' => $scannerStatus,
            'summary' => $summary,
            'documents' => $results,
            'orphaned_sources' => $orphanedSources,
        ];

        File::put($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));

        return $path;
    }
}
