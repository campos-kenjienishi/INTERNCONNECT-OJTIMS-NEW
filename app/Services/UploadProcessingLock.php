<?php

namespace App\Services;

use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Facades\Cache;

class UploadProcessingLock
{
    public function create(int $userId, string $operation): Lock
    {
        $normalizedOperation = mb_strtolower(trim($operation));

        return Cache::lock(
            'upload-processing:'.$userId.':'.hash('sha256', $normalizedOperation),
            60
        );
    }
}
