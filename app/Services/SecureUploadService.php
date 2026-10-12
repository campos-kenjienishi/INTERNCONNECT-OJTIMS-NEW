<?php

namespace App\Services;

use App\Exceptions\UnsafeUploadException;
use App\Services\Uploads\MalwareScanner;
use App\Services\Uploads\SecureUploadInspection;
use App\Services\Uploads\SecureUploadResult;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;
use ZipArchive;

class SecureUploadService
{
    public function __construct(
        private readonly MalwareScanner $malwareScanner,
        private readonly ?UploadSecurityRecorder $securityRecorder = null
    ) {}

    public function store(UploadedFile $file, string $profile, string $destination): SecureUploadResult
    {
        $policy = config("upload_security.profiles.{$profile}");

        if (! is_array($policy)) {
            throw new UnsafeUploadException('invalid_profile', 'The upload security policy is not configured.');
        }

        $destination = trim($destination, '/\\');

        if ($destination === '' || preg_match('#^[A-Za-z0-9_-]+(?:/[A-Za-z0-9_-]+)*$#', str_replace('\\', '/', $destination)) !== 1) {
            throw new UnsafeUploadException('invalid_destination', 'The upload destination is invalid.');
        }

        $destination = str_replace('\\', '/', $destination);

        if (! $file->isValid()) {
            throw new UnsafeUploadException('upload_failed', 'The file upload did not complete successfully.');
        }

        $size = (int) $file->getSize();
        $maxBytes = (int) ($policy['max_bytes'] ?? config('upload_security.max_bytes'));

        if ($size < 1) {
            throw new UnsafeUploadException('empty_file', 'Empty files cannot be uploaded.');
        }

        if ($size > $maxBytes) {
            throw new UnsafeUploadException('file_too_large', 'The uploaded file exceeds the allowed size.');
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $allowedTypes = $policy['types'] ?? [];

        if ($extension === '' || ! array_key_exists($extension, $allowedTypes)) {
            throw new UnsafeUploadException('extension_not_allowed', 'This file type is not allowed.');
        }

        $disk = Storage::disk((string) config('upload_security.disk', 'local'));
        $quarantineDirectory = trim((string) config('upload_security.quarantine_directory'), '/\\');
        $quarantineName = Str::uuid()->toString().'.upload';
        $quarantinePath = $disk->putFileAs($quarantineDirectory, $file, $quarantineName);

        if ($quarantinePath === false) {
            throw new UnsafeUploadException('storage_failed', 'The uploaded file could not be stored for inspection.');
        }

        try {
            $absolutePath = $disk->path($quarantinePath);
            $inspection = $this->inspectExisting($absolutePath, $file->getClientOriginalName(), $profile);

            $filename = Str::uuid()->toString().'.'.$inspection->extension;
            $storageDirectory = trim((string) config('upload_security.storage_directory'), '/\\');
            $finalPath = implode('/', array_filter([
                $storageDirectory,
                $destination,
                $filename,
            ], fn (string $part) => $part !== ''));

            if (! $disk->move($quarantinePath, $finalPath)) {
                throw new UnsafeUploadException('storage_failed', 'The verified file could not be stored.');
            }

            $storedHash = hash_file('sha256', $disk->path($finalPath));

            if ($storedHash === false || ! hash_equals($inspection->sha256, $storedHash)) {
                $disk->delete($finalPath);
                throw new UnsafeUploadException('storage_failed', 'The verified file could not be checksummed.');
            }

            $securityRecordId = null;

            if (config('upload_security.evidence_enabled')) {
                try {
                    $securityRecord = ($this->securityRecorder ?? app(UploadSecurityRecorder::class))->recordStored(
                        $finalPath,
                        $filename,
                        $destination,
                        $profile,
                        $inspection,
                        'new_upload'
                    );
                    $securityRecordId = (int) $securityRecord->getKey();
                } catch (Throwable $exception) {
                    $disk->delete($finalPath);
                    Log::error('Upload was rejected because security evidence could not be recorded.', [
                        'exception' => $exception::class,
                        'profile' => $profile,
                        'extension' => $inspection->extension,
                    ]);

                    throw new UnsafeUploadException(
                        'security_record_failed',
                        'The uploaded file could not be securely registered. Please try again later.'
                    );
                }
            }

            return new SecureUploadResult(
                $finalPath,
                $filename,
                $inspection->extension,
                $inspection->mimeType,
                $inspection->size,
                $inspection->sha256,
                $inspection->scanStatus,
                $securityRecordId
            );
        } finally {
            if ($disk->exists($quarantinePath)) {
                $disk->delete($quarantinePath);
            }
        }
    }

    public function inspectExisting(string $path, string $originalFilename, string $profile): SecureUploadInspection
    {
        $policy = config("upload_security.profiles.{$profile}");

        if (! is_array($policy)) {
            throw new UnsafeUploadException('invalid_profile', 'The upload security policy is not configured.');
        }

        if (! is_file($path) || ! is_readable($path)) {
            throw new UnsafeUploadException('file_unreadable', 'The uploaded file could not be inspected.');
        }

        $size = filesize($path);
        $maxBytes = (int) ($policy['max_bytes'] ?? config('upload_security.max_bytes'));

        if ($size === false || $size < 1) {
            throw new UnsafeUploadException('empty_file', 'Empty files cannot be uploaded.');
        }

        if ($size > $maxBytes) {
            throw new UnsafeUploadException('file_too_large', 'The uploaded file exceeds the allowed size.');
        }

        $extension = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
        $allowedTypes = $policy['types'] ?? [];

        if ($extension === '' || ! array_key_exists($extension, $allowedTypes)) {
            throw new UnsafeUploadException('extension_not_allowed', 'This file type is not allowed.');
        }

        $mimeType = $this->detectMimeType($path);
        $allowedMimeTypes = $allowedTypes[$extension]['mime_types'] ?? [];

        if (! in_array($mimeType, $allowedMimeTypes, true)) {
            throw new UnsafeUploadException(
                'mime_mismatch',
                "Upload rejected: the file content does not match the .{$extension} extension. Renaming a different file type to .{$extension} is not allowed."
            );
        }

        $this->validateStructure($path, $extension, (int) $size, $policy);
        $sha256 = hash_file('sha256', $path);

        if ($sha256 === false) {
            throw new UnsafeUploadException('file_unreadable', 'The uploaded file could not be checksummed.');
        }

        try {
            $scan = $this->malwareScanner->scan($path);
        } catch (UnsafeUploadException $exception) {
            Log::error('Upload malware scan could not be completed.', [
                'reason' => $exception->reason,
                'profile' => $profile,
                'extension' => $extension,
                'size' => $size,
            ]);

            throw $exception;
        } catch (Throwable $exception) {
            Log::error('Upload malware scanner failed unexpectedly.', [
                'exception' => $exception::class,
                'profile' => $profile,
                'extension' => $extension,
                'size' => $size,
            ]);

            throw new UnsafeUploadException(
                'scanner_error',
                'The security scanner could not verify the uploaded file.'
            );
        }

        if (! $scan->clean) {
            Log::warning('Malware was detected in a quarantined upload.', [
                'threat' => $scan->threat,
                'profile' => $profile,
                'extension' => $extension,
                'size' => $size,
                'sha256' => $sha256,
            ]);

            throw new UnsafeUploadException('malware_detected', 'The uploaded file failed the malware security scan.');
        }

        return new SecureUploadInspection(
            $extension,
            $mimeType,
            (int) $size,
            $sha256,
            $scan->status
        );
    }

    private function detectMimeType(string $path): string
    {
        $fileInfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $fileInfo->file($path);

        if (! is_string($mimeType) || $mimeType === '') {
            throw new UnsafeUploadException('mime_unknown', 'The uploaded file type could not be identified.');
        }

        return strtolower($mimeType);
    }

    private function validateStructure(string $path, string $extension, int $size, array $policy): void
    {
        if ($extension === 'pdf') {
            $this->validatePdf($path, $size);

            return;
        }

        if (in_array($extension, ['doc', 'xls'], true)) {
            $this->validateCompoundDocument($path);

            return;
        }

        if (in_array($extension, ['docx', 'xlsx'], true)) {
            $this->validateOpenXmlDocument($path, $extension, $policy);
        }
    }

    private function validatePdf(string $path, int $size): void
    {
        $file = fopen($path, 'rb');

        if ($file === false) {
            throw new UnsafeUploadException('file_unreadable', 'The uploaded file could not be inspected.');
        }

        try {
            $header = fread($file, 8);

            if (! is_string($header) || preg_match('/^%PDF-[12]\.\d/', $header) !== 1) {
                throw new UnsafeUploadException('invalid_pdf', 'The uploaded file is not a valid PDF document.');
            }

            $tailLength = min($size, 4096);
            fseek($file, -$tailLength, SEEK_END);
            $tail = fread($file, $tailLength);

            if (! is_string($tail) || ! str_contains($tail, '%%EOF')) {
                throw new UnsafeUploadException('invalid_pdf', 'The PDF document is incomplete or malformed.');
            }
        } finally {
            fclose($file);
        }
    }

    private function validateCompoundDocument(string $path): void
    {
        $file = fopen($path, 'rb');

        if ($file === false) {
            throw new UnsafeUploadException('file_unreadable', 'The uploaded file could not be inspected.');
        }

        try {
            $header = fread($file, 8);
        } finally {
            fclose($file);
        }

        if ($header !== hex2bin('D0CF11E0A1B11AE1')) {
            throw new UnsafeUploadException('invalid_office_document', 'The uploaded Office document is malformed.');
        }
    }

    private function validateOpenXmlDocument(string $path, string $extension, array $policy): void
    {
        $archive = new ZipArchive;

        if ($archive->open($path, ZipArchive::CHECKCONS) !== true) {
            throw new UnsafeUploadException('invalid_office_document', 'The uploaded Office document is malformed.');
        }

        try {
            $requiredEntry = $extension === 'docx' ? 'word/document.xml' : 'xl/workbook.xml';

            if ($archive->locateName('[Content_Types].xml') === false || $archive->locateName($requiredEntry) === false) {
                throw new UnsafeUploadException('invalid_office_document', 'The uploaded Office document does not match its extension.');
            }

            $maxExpandedBytes = (int) ($policy['max_expanded_bytes'] ?? config('upload_security.max_expanded_bytes'));
            $expandedBytes = 0;

            for ($index = 0; $index < $archive->numFiles; $index++) {
                $entry = $archive->statIndex($index);

                if (! is_array($entry) || ! isset($entry['size'])) {
                    throw new UnsafeUploadException('invalid_office_document', 'The uploaded Office document is malformed.');
                }

                $expandedBytes += (int) $entry['size'];

                if ($expandedBytes > $maxExpandedBytes) {
                    throw new UnsafeUploadException('expanded_file_too_large', 'The uploaded Office document expands beyond the allowed size.');
                }
            }
        } finally {
            $archive->close();
        }
    }
}
