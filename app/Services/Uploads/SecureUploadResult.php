<?php

namespace App\Services\Uploads;

class SecureUploadResult
{
    public function __construct(
        public readonly string $path,
        public readonly string $filename,
        public readonly string $extension,
        public readonly string $mimeType,
        public readonly int $size,
        public readonly string $sha256,
        public readonly string $scanStatus,
        public readonly ?int $securityRecordId = null
    ) {}
}
