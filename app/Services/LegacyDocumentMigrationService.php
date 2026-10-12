<?php

namespace App\Services;

use App\Exceptions\UnsafeUploadException;
use App\Services\Uploads\LegacyMigrationResult;
use App\Services\Uploads\SecureUploadInspection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class LegacyDocumentMigrationService
{
    public function __construct(
        private readonly SecureUploadService $secureUploads,
        private readonly ?UploadSecurityRecorder $securityRecorder = null
    ) {}

    public function migrate(
        string $directory,
        string $filename,
        string $profile,
        bool $execute,
        bool $allowUnscanned
    ): LegacyMigrationResult {
        $directory = trim(str_replace('\\', '/', $directory), '/');
        $filename = trim($filename);

        if (preg_match('#^[A-Za-z0-9_-]+(?:/[A-Za-z0-9_-]+)*$#', $directory) !== 1) {
            return new LegacyMigrationResult('unsafe_destination', $directory, $filename, reason: 'invalid_destination');
        }

        if ($filename === '' || basename(str_replace('\\', '/', $filename)) !== $filename) {
            return new LegacyMigrationResult('unsafe_filename', $directory, $filename, reason: 'invalid_filename');
        }

        $legacyRoot = realpath((string) config('upload_security.legacy_directory'));

        if ($legacyRoot === false) {
            return new LegacyMigrationResult('missing_source', $directory, $filename, reason: 'legacy_directory_missing');
        }

        $source = $legacyRoot.DIRECTORY_SEPARATOR.$filename;
        $realSource = realpath($source);

        if ($realSource === false || ! is_file($realSource)) {
            return new LegacyMigrationResult('missing_source', $directory, $filename, reason: 'file_missing');
        }

        if (is_link($source) || dirname($realSource) !== $legacyRoot) {
            return new LegacyMigrationResult('unsafe_source', $directory, $filename, reason: 'source_outside_legacy_directory');
        }

        try {
            $inspection = $this->secureUploads->inspectExisting($realSource, $filename, $profile);
        } catch (UnsafeUploadException $exception) {
            return new LegacyMigrationResult('security_rejected', $directory, $filename, reason: $exception->reason);
        } catch (Throwable) {
            return new LegacyMigrationResult('security_rejected', $directory, $filename, reason: 'inspection_failed');
        }

        if ($inspection->scanStatus === 'disabled' && ! $allowUnscanned) {
            return new LegacyMigrationResult(
                'unscanned',
                $directory,
                $filename,
                $inspection->sha256,
                $inspection->size,
                $inspection->scanStatus,
                'malware_scan_required'
            );
        }

        $disk = Storage::disk((string) config('upload_security.disk'));
        $destination = $directory.'/'.$filename;

        if ($disk->exists($destination)) {
            $destinationHash = $this->hashStoredFile($destination);
            $matches = hash_equals($inspection->sha256, $destinationHash ?? '');

            if ($matches && ! $this->recordEvidence($destination, $filename, $directory, $profile, $inspection)) {
                return new LegacyMigrationResult('evidence_failed', $directory, $filename, reason: 'security_record_failed');
            }

            return new LegacyMigrationResult(
                $matches ? 'already_migrated' : 'conflict',
                $directory,
                $filename,
                $inspection->sha256,
                $inspection->size,
                $inspection->scanStatus,
                $destinationHash === null ? 'destination_unreadable' : null
            );
        }

        if (! $execute) {
            return new LegacyMigrationResult(
                'pending',
                $directory,
                $filename,
                $inspection->sha256,
                $inspection->size,
                $inspection->scanStatus
            );
        }

        $temporaryPath = '.migration/'.Str::uuid()->toString().'.part';

        try {
            $sourceStream = fopen($realSource, 'rb');

            if ($sourceStream === false) {
                return new LegacyMigrationResult('copy_failed', $directory, $filename, reason: 'source_unreadable');
            }

            try {
                if (! $disk->put($temporaryPath, $sourceStream)) {
                    return new LegacyMigrationResult('copy_failed', $directory, $filename, reason: 'temporary_write_failed');
                }
            } finally {
                fclose($sourceStream);
            }

            $temporaryHash = $this->hashStoredFile($temporaryPath);

            if ($temporaryHash === null || ! hash_equals($inspection->sha256, $temporaryHash)) {
                return new LegacyMigrationResult('verification_failed', $directory, $filename, reason: 'temporary_checksum_mismatch');
            }

            if ($disk->exists($destination)) {
                $destinationHash = $this->hashStoredFile($destination);
                $matches = hash_equals($inspection->sha256, $destinationHash ?? '');

                if ($matches && ! $this->recordEvidence($destination, $filename, $directory, $profile, $inspection)) {
                    return new LegacyMigrationResult('evidence_failed', $directory, $filename, reason: 'security_record_failed');
                }

                return new LegacyMigrationResult(
                    $matches ? 'already_migrated' : 'conflict',
                    $directory,
                    $filename,
                    $inspection->sha256,
                    $inspection->size,
                    $inspection->scanStatus,
                    'destination_created_during_migration'
                );
            }

            if (! $disk->move($temporaryPath, $destination)) {
                return new LegacyMigrationResult('copy_failed', $directory, $filename, reason: 'final_move_failed');
            }

            $destinationHash = $this->hashStoredFile($destination);

            if ($destinationHash === null || ! hash_equals($inspection->sha256, $destinationHash)) {
                return new LegacyMigrationResult(
                    'verification_failed',
                    $directory,
                    $filename,
                    $inspection->sha256,
                    $inspection->size,
                    $inspection->scanStatus,
                    'destination_checksum_mismatch'
                );
            }

            if (! $this->recordEvidence($destination, $filename, $directory, $profile, $inspection)) {
                return new LegacyMigrationResult('evidence_failed', $directory, $filename, reason: 'security_record_failed');
            }

            return new LegacyMigrationResult(
                'migrated',
                $directory,
                $filename,
                $inspection->sha256,
                $inspection->size,
                $inspection->scanStatus
            );
        } catch (Throwable) {
            return new LegacyMigrationResult('copy_failed', $directory, $filename, reason: 'storage_error');
        } finally {
            if ($disk->exists($temporaryPath)) {
                $disk->delete($temporaryPath);
            }
        }
    }

    private function recordEvidence(
        string $path,
        string $filename,
        string $directory,
        string $profile,
        SecureUploadInspection $inspection
    ): bool {
        if (! config('upload_security.evidence_enabled')) {
            return true;
        }

        try {
            ($this->securityRecorder ?? app(UploadSecurityRecorder::class))->recordStored(
                $path,
                $filename,
                $directory,
                $profile,
                $inspection,
                'legacy_migration'
            );

            return true;
        } catch (Throwable $exception) {
            Log::error('Legacy upload security evidence could not be recorded.', [
                'path' => $path,
                'exception' => $exception::class,
            ]);

            return false;
        }
    }

    private function hashStoredFile(string $path): ?string
    {
        $disk = Storage::disk((string) config('upload_security.disk'));
        $stream = $disk->readStream($path);

        if ($stream === false) {
            return null;
        }

        try {
            $context = hash_init('sha256');
            hash_update_stream($context, $stream);

            return hash_final($context);
        } finally {
            fclose($stream);
        }
    }
}
