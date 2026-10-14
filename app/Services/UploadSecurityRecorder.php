<?php

namespace App\Services;

use App\Models\UploadSecurityRecord;
use App\Services\Uploads\SecureUploadInspection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use RuntimeException;
use Throwable;

class UploadSecurityRecorder
{
    public function recordStored(
        string $path,
        string $filename,
        string $directory,
        string $profile,
        SecureUploadInspection $inspection,
        string $origin
    ): UploadSecurityRecord {
        $scannerDriver = config('upload_security.malware_scan.enabled') ? 'clamav' : 'none';
        $hostingScanner = trim((string) config('upload_security.hosting_scanner')) ?: null;

        $record = UploadSecurityRecord::firstOrCreate(
            [
                'storage_disk' => (string) config('upload_security.disk'),
                'storage_path' => $path,
            ],
            [
                'filename' => $filename,
                'directory' => $directory,
                'profile' => $profile,
                'extension' => $inspection->extension,
                'mime_type' => $inspection->mimeType,
                'size_bytes' => $inspection->size,
                'sha256' => $inspection->sha256,
                'validation_status' => 'validated',
                'scanner_driver' => $scannerDriver,
                'scan_status' => $inspection->scanStatus,
                'scanned_at' => $inspection->scanStatus === 'clean' ? now() : null,
                'hosting_scanner' => $hostingScanner,
                'hosting_scan_status' => $hostingScanner === null ? null : 'provider_managed',
                'uploaded_by_user_id' => Session::get('loginId'),
                'origin' => $origin,
                'lifecycle_status' => 'active',
                'last_verified_at' => now(),
            ]
        );

        if (! $record->wasRecentlyCreated) {
            if (! hash_equals($record->sha256, $inspection->sha256)) {
                throw new RuntimeException('The storage path is already registered with a different checksum.');
            }

            $record->update([
                'lifecycle_status' => 'active',
                'last_verified_at' => now(),
                'retired_at' => null,
            ]);
        }

        return $record;
    }

    public function link(?int $recordId, Model $source, ?int $uploaderUserId = null): void
    {
        if ($recordId === null) {
            return;
        }

        try {
            UploadSecurityRecord::whereKey($recordId)->update([
                'source_type' => $source::class,
                'source_id' => $source->getKey(),
                'uploaded_by_user_id' => $uploaderUserId ?? Session::get('loginId'),
            ]);
        } catch (Throwable $exception) {
            Log::error('Upload security evidence could not be linked to its source record.', [
                'security_record_id' => $recordId,
                'source_type' => $source::class,
                'source_id' => $source->getKey(),
                'exception' => $exception::class,
            ]);
        }
    }

    public function retire(string $directory, string $filename): void
    {
        try {
            UploadSecurityRecord::where('storage_disk', (string) config('upload_security.disk'))
                ->where('storage_path', trim($directory, '/\\').'/'.$filename)
                ->where('lifecycle_status', 'active')
                ->update([
                    'lifecycle_status' => 'retired',
                    'retired_at' => now(),
                ]);
        } catch (Throwable $exception) {
            Log::error('Upload security evidence could not be retired.', [
                'directory' => $directory,
                'filename' => $filename,
                'exception' => $exception::class,
            ]);
        }
    }
}
