<?php

namespace App\Services\Uploads;

class LegacyMigrationResult
{
    public function __construct(
        public readonly string $status,
        public readonly string $directory,
        public readonly string $filename,
        public readonly ?string $sha256 = null,
        public readonly ?int $size = null,
        public readonly ?string $scanStatus = null,
        public readonly ?string $reason = null
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'status' => $this->status,
            'directory' => $this->directory,
            'filename' => $this->filename,
            'sha256' => $this->sha256,
            'size' => $this->size,
            'scan_status' => $this->scanStatus,
            'reason' => $this->reason,
        ], fn ($value) => $value !== null);
    }
}
