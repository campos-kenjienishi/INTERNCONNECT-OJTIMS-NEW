<?php

namespace Tests\Unit;

use App\Models\UploadSecurityRecord;
use App\Services\LegacyDocumentMigrationService;
use App\Services\SecureUploadService;
use App\Services\Uploads\NullMalwareScanner;
use App\Services\UploadSecurityRecorder;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LegacyDocumentMigrationServiceTest extends TestCase
{
    private string $legacyDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        config()->set('upload_security.disk', 'local');
        config()->set('upload_security.evidence_enabled', false);
        $this->legacyDirectory = storage_path('framework/testing/legacy-'.uniqid());
        mkdir($this->legacyDirectory, 0777, true);
        config()->set('upload_security.legacy_directory', $this->legacyDirectory);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->legacyDirectory)) {
            foreach (glob($this->legacyDirectory.'/*') ?: [] as $file) {
                @unlink($file);
            }

            @rmdir($this->legacyDirectory);
        }

        parent::tearDown();
    }

    public function test_it_copies_and_verifies_a_legacy_file_without_deleting_the_source(): void
    {
        $contents = $this->validPdf();
        file_put_contents($this->legacyDirectory.'/legacy.pdf', $contents);

        $result = $this->service()->migrate('requirements', 'legacy.pdf', 'pdf', true, true);

        $this->assertSame('migrated', $result->status);
        $this->assertFileExists($this->legacyDirectory.'/legacy.pdf');
        $this->assertSame($contents, file_get_contents($this->legacyDirectory.'/legacy.pdf'));
        Storage::disk('local')->assertExists('requirements/legacy.pdf');
        $this->assertSame(hash('sha256', $contents), $result->sha256);
        $this->assertSame($contents, Storage::disk('local')->get('requirements/legacy.pdf'));
    }

    public function test_dry_run_does_not_copy_the_file(): void
    {
        file_put_contents($this->legacyDirectory.'/legacy.pdf', $this->validPdf());

        $result = $this->service()->migrate('requirements', 'legacy.pdf', 'pdf', false, true);

        $this->assertSame('pending', $result->status);
        Storage::disk('local')->assertMissing('requirements/legacy.pdf');
        $this->assertFileExists($this->legacyDirectory.'/legacy.pdf');
    }

    public function test_it_is_idempotent_when_the_private_copy_matches(): void
    {
        file_put_contents($this->legacyDirectory.'/legacy.pdf', $this->validPdf());
        $service = $this->service();

        $this->assertSame('migrated', $service->migrate('requirements', 'legacy.pdf', 'pdf', true, true)->status);
        $this->assertSame('already_migrated', $service->migrate('requirements', 'legacy.pdf', 'pdf', true, true)->status);
    }

    public function test_it_never_overwrites_a_conflicting_private_file(): void
    {
        file_put_contents($this->legacyDirectory.'/legacy.pdf', $this->validPdf());
        Storage::disk('local')->put('requirements/legacy.pdf', 'different existing file');

        $result = $this->service()->migrate('requirements', 'legacy.pdf', 'pdf', true, true);

        $this->assertSame('conflict', $result->status);
        $this->assertSame('different existing file', Storage::disk('local')->get('requirements/legacy.pdf'));
        $this->assertFileExists($this->legacyDirectory.'/legacy.pdf');
    }

    public function test_it_requires_explicit_permission_when_scanning_is_disabled(): void
    {
        file_put_contents($this->legacyDirectory.'/legacy.pdf', $this->validPdf());

        $result = $this->service()->migrate('requirements', 'legacy.pdf', 'pdf', true, false);

        $this->assertSame('unscanned', $result->status);
        Storage::disk('local')->assertMissing('requirements/legacy.pdf');
    }

    public function test_it_records_security_evidence_for_a_migrated_file(): void
    {
        config()->set('upload_security.evidence_enabled', true);
        file_put_contents($this->legacyDirectory.'/legacy.pdf', $this->validPdf());
        $record = new UploadSecurityRecord;
        $record->setAttribute('id', 44);
        $record->exists = true;
        $recorder = \Mockery::mock(UploadSecurityRecorder::class);
        $recorder->shouldReceive('recordStored')
            ->once()
            ->withArgs(fn ($path, $filename, $directory, $profile, $inspection, $origin) => $path === 'requirements/legacy.pdf'
                && $filename === 'legacy.pdf'
                && $directory === 'requirements'
                && $profile === 'pdf'
                && $inspection->sha256 === hash('sha256', $this->validPdf())
                && $origin === 'legacy_migration')
            ->andReturn($record);
        $service = new LegacyDocumentMigrationService(
            new SecureUploadService(new NullMalwareScanner),
            $recorder
        );

        $result = $service->migrate('requirements', 'legacy.pdf', 'pdf', true, true);

        $this->assertSame('migrated', $result->status);
        $this->assertFileExists($this->legacyDirectory.'/legacy.pdf');
    }

    public function test_it_rejects_unsafe_filenames_and_destinations(): void
    {
        $service = $this->service();

        $this->assertSame('unsafe_filename', $service->migrate('requirements', '../secret.pdf', 'pdf', true, true)->status);
        $this->assertSame('unsafe_destination', $service->migrate('../public', 'legacy.pdf', 'pdf', true, true)->status);
    }

    private function service(): LegacyDocumentMigrationService
    {
        return new LegacyDocumentMigrationService(new SecureUploadService(new NullMalwareScanner));
    }

    private function validPdf(): string
    {
        return "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n<< /Root 1 0 R >>\n%%EOF\n";
    }
}
