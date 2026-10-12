<?php

namespace App\Services;

use App\Models\Company;
use Carbon\CarbonImmutable;

class MoaValidityService
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_EXPIRING_SOON = 'expiring_soon';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_UNKNOWN = 'unknown';

    public function describe(Company $company, int $expiringSoonDays = 60): array
    {
        $validUntil = $this->parseDate($company->valid_until);
        $today = CarbonImmutable::today();

        if ($validUntil === null) {
            return [
                'status' => self::STATUS_UNKNOWN,
                'label' => 'Unknown',
                'valid_until' => null,
                'days_remaining' => null,
            ];
        }

        if ($validUntil->isBefore($today)) {
            $status = self::STATUS_EXPIRED;
            $label = 'Expired';
        } elseif ($validUntil->lessThanOrEqualTo($today->addDays($expiringSoonDays))) {
            $status = self::STATUS_EXPIRING_SOON;
            $label = 'Expiring Soon';
        } else {
            $status = self::STATUS_ACTIVE;
            $label = 'Active';
        }

        return [
            'status' => $status,
            'label' => $label,
            'valid_until' => $validUntil,
            'days_remaining' => $today->diffInDays($validUntil, false),
        ];
    }

    public function isActive(Company $company): bool
    {
        return in_array($this->describe($company)['status'], [
            self::STATUS_ACTIVE,
            self::STATUS_EXPIRING_SOON,
        ], true);
    }

    public function parseDate(mixed $value): ?CarbonImmutable
    {
        if (empty($value)) {
            return null;
        }

        try {
            return CarbonImmutable::parse($value)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
}
