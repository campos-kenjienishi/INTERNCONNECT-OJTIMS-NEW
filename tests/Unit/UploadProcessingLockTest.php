<?php

namespace Tests\Unit;

use App\Services\UploadProcessingLock;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class UploadProcessingLockTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function test_it_blocks_the_same_upload_operation_while_it_is_processing(): void
    {
        $guard = new UploadProcessingLock();
        $firstLock = $guard->create(15, 'POST:uploadfile:[]');
        $duplicateLock = $guard->create(15, ' post:uploadfile:[] ');

        $this->assertTrue($firstLock->get());
        $this->assertFalse($duplicateLock->get());

        $firstLock->release();

        $this->assertTrue($duplicateLock->get());
        $duplicateLock->release();
    }

    public function test_it_allows_different_users_and_operations_to_process_independently(): void
    {
        $guard = new UploadProcessingLock();
        $firstUpload = $guard->create(15, 'POST:uploadfile:[]');
        $differentUser = $guard->create(16, 'POST:uploadfile:[]');
        $differentOperation = $guard->create(15, 'POST:companyCreate:[]');

        $this->assertTrue($firstUpload->get());
        $this->assertTrue($differentUser->get());
        $this->assertTrue($differentOperation->get());

        $firstUpload->release();
        $differentUser->release();
        $differentOperation->release();
    }
}
