<?php

namespace App\Services;

use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Facades\Cache;

class RequirementUploadLock
{
    public function create(int $userId, string $requirementName): Lock
    {
        $normalizedRequirement = mb_strtolower(trim($requirementName));

        return Cache::lock(
            'requirement-upload:'.$userId.':'.hash('sha256', $normalizedRequirement),
            60
        );
    }
}
