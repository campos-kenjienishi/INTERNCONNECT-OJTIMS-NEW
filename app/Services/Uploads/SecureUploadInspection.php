<?php

namespace App\Services\Uploads;

class SecureUploadInspection
{
    public function __construct(
        public readonly string $extension,
        public readonly string $mimeType,
        public readonly int $size,
        public readonly string $sha256,
        public readonly string $scanStatus
    ) {}
}
