<?php

namespace Tests\Unit;

use App\Exceptions\UnsafeUploadException;
use App\Services\Uploads\ClamAvScanner;
use PHPUnit\Framework\TestCase;

class ClamAvScannerTest extends TestCase
{
    public function test_invalid_connection_settings_report_an_unavailable_status(): void
    {
        $status = (new ClamAvScanner('', 0, 0))->status();

        $this->assertFalse($status->ready);
        $this->assertSame('unavailable', $status->status);
    }

    public function test_invalid_connection_settings_fail_closed_during_a_scan(): void
    {
        $scanner = new ClamAvScanner('', 0, 0);

        try {
            $scanner->scan(__FILE__);
            $this->fail('Expected the invalid scanner configuration to reject the upload.');
        } catch (UnsafeUploadException $exception) {
            $this->assertSame('scanner_configuration', $exception->reason);
        }
    }
}
