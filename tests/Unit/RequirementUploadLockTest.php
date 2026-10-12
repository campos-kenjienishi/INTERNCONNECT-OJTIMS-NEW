<?php

namespace Tests\Unit;

use App\Services\RequirementUploadLock;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class RequirementUploadLockTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function test_it_blocks_a_duplicate_upload_for_the_same_student_and_requirement(): void
    {
        $guard = new RequirementUploadLock();
        $firstLock = $guard->create(15, 'Resume');
        $duplicateLock = $guard->create(15, ' resume ');

        $this->assertTrue($firstLock->get());
        $this->assertFalse($duplicateLock->get());

        $firstLock->release();

        $this->assertTrue($duplicateLock->get());
        $duplicateLock->release();
    }

    public function test_it_allows_different_requirements_to_process_independently(): void
    {
        $guard = new RequirementUploadLock();
        $resumeLock = $guard->create(15, 'Resume');
        $medicalLock = $guard->create(15, 'Medical Certificate');

        $this->assertTrue($resumeLock->get());
        $this->assertTrue($medicalLock->get());

        $resumeLock->release();
        $medicalLock->release();
    }
}
