<?php

namespace Tests\Feature;

use App\Services\Uploads\MalwareScanner;
use App\Services\Uploads\MalwareScannerStatus;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class UploadScannerStatusTest extends TestCase
{
    public function test_disabled_scanning_is_allowed_when_not_required(): void
    {
        config()->set('upload_security.malware_scan.enabled', false);
        config()->set('upload_security.malware_scan.required', false);
        config()->set('upload_security.hosting_scanner', 'monarx');
        $this->app->forgetInstance(MalwareScanner::class);

        $this->assertSame(0, Artisan::call('uploads:scanner-status'));
        $output = Artisan::output();

        $this->assertStringContainsString('disabled', $output);
        $this->assertStringContainsString('monarx hosting-level protection', $output);
    }

    public function test_uploads_are_blocked_when_scanning_is_required_but_disabled(): void
    {
        config()->set('upload_security.malware_scan.enabled', false);
        config()->set('upload_security.malware_scan.required', true);
        $this->app->forgetInstance(MalwareScanner::class);

        $this->assertSame(1, Artisan::call('uploads:scanner-status'));
        $this->assertStringContainsString('blocked', Artisan::output());
    }

    public function test_required_scanning_passes_when_the_scanner_is_ready(): void
    {
        config()->set('upload_security.malware_scan.required', true);
        $this->app->instance(MalwareScanner::class, new class implements MalwareScanner
        {
            public function scan(string $path): \App\Services\Uploads\MalwareScanResult
            {
                return \App\Services\Uploads\MalwareScanResult::clean();
            }

            public function status(): MalwareScannerStatus
            {
                return MalwareScannerStatus::ready('Test scanner is ready.');
            }
        });

        $this->assertSame(0, Artisan::call('uploads:scanner-status'));
        $this->assertStringContainsString('operational', Artisan::output());
    }
}
