<?php

namespace Tests\Unit;

use App\Models\Company;
use App\Services\MoaValidityService;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class MoaValidityServiceTest extends TestCase
{
    private MoaValidityService $service;

    protected function setUp(): void
    {
        parent::setUp();

        CarbonImmutable::setTestNow('2026-10-12 12:00:00');
        $this->service = new MoaValidityService();
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    public function test_it_treats_the_expiration_date_as_active_for_the_entire_day(): void
    {
        $company = new Company();
        $company->valid_until = '2026-10-12';

        $result = $this->service->describe($company);

        $this->assertSame(MoaValidityService::STATUS_EXPIRING_SOON, $result['status']);
        $this->assertSame(0, $result['days_remaining']);
        $this->assertTrue($this->service->isActive($company));
    }

    public function test_it_marks_moas_within_sixty_days_as_expiring_soon(): void
    {
        $company = new Company();
        $company->valid_until = '2026-11-15';

        $result = $this->service->describe($company);

        $this->assertSame(MoaValidityService::STATUS_EXPIRING_SOON, $result['status']);
        $this->assertSame(34, $result['days_remaining']);
    }

    public function test_it_distinguishes_active_expired_and_unknown_moas(): void
    {
        $active = new Company();
        $active->valid_until = '2027-10-12';
        $expired = new Company();
        $expired->valid_until = '2026-10-11';
        $unknown = new Company();
        $unknown->valid_until = null;

        $this->assertSame(MoaValidityService::STATUS_ACTIVE, $this->service->describe($active)['status']);
        $this->assertSame(MoaValidityService::STATUS_EXPIRED, $this->service->describe($expired)['status']);
        $this->assertSame(MoaValidityService::STATUS_UNKNOWN, $this->service->describe($unknown)['status']);
    }
}
