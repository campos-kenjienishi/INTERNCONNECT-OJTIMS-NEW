<?php

namespace Tests\Feature;

use App\Models\UploadedFile;
use App\Models\UploadSecurityRecord;
use App\Services\Uploads\SecureUploadInspection;
use App\Services\UploadSecurityRecorder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadSecurityEvidenceTest extends TestCase
{
    private string $originalConnection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalConnection = (string) config('database.default');
        config()->set('database.connections.security_testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        config()->set('database.default', 'security_testing');
        DB::purge('security_testing');
        Schema::connection('security_testing')->create('upload_security_records', function (Blueprint $table) {
            $table->id();
            $table->string('storage_disk', 50);
            $table->string('storage_path')->unique();
            $table->string('filename');
            $table->string('directory', 100);
            $table->string('profile', 50);
            $table->string('extension', 20);
            $table->string('mime_type', 150);
            $table->unsignedBigInteger('size_bytes');
            $table->char('sha256', 64);
            $table->string('validation_status', 30);
            $table->string('scanner_driver', 50);
            $table->string('scan_status', 30);
            $table->timestamp('scanned_at')->nullable();
            $table->string('hosting_scanner', 50)->nullable();
            $table->string('hosting_scan_status', 30)->nullable();
            $table->string('source_type', 100)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedBigInteger('uploaded_by_user_id')->nullable();
            $table->string('origin', 30);
            $table->string('lifecycle_status', 30);
            $table->timestamp('last_verified_at')->nullable();
            $table->timestamp('retired_at')->nullable();
            $table->timestamps();
        });

        Storage::fake('local');
        config()->set('upload_security.disk', 'local');
        config()->set('upload_security.malware_scan.enabled', false);
        config()->set('upload_security.hosting_scanner', 'monarx');
    }

    protected function tearDown(): void
    {
        DB::purge('security_testing');
        config()->set('database.default', $this->originalConnection);

        parent::tearDown();
    }

    public function test_it_records_links_and_retires_security_evidence_without_deleting_it(): void
    {
        Session::put('loginId', 42);
        $contents = 'verified document';
        $inspection = new SecureUploadInspection(
            'pdf',
            'application/pdf',
            strlen($contents),
            hash('sha256', $contents),
            'disabled'
        );
        $recorder = new UploadSecurityRecorder;

        $record = $recorder->recordStored(
            'requirements/document.pdf',
            'document.pdf',
            'requirements',
            'pdf',
            $inspection,
            'new_upload'
        );
        $source = new UploadedFile;
        $source->setAttribute('id', 91);
        $source->exists = true;
        $recorder->link($record->id, $source, 42);
        $recorder->retire('requirements', 'document.pdf');

        $record->refresh();
        $this->assertSame('none', $record->scanner_driver);
        $this->assertSame('disabled', $record->scan_status);
        $this->assertSame('monarx', $record->hosting_scanner);
        $this->assertSame('provider_managed', $record->hosting_scan_status);
        $this->assertSame(UploadedFile::class, $record->source_type);
        $this->assertSame(91, $record->source_id);
        $this->assertSame(42, $record->uploaded_by_user_id);
        $this->assertSame('retired', $record->lifecycle_status);
        $this->assertNotNull($record->retired_at);
        $this->assertDatabaseCount('upload_security_records', 1);
    }

    public function test_integrity_command_detects_tampering_without_modifying_the_file(): void
    {
        Storage::disk('local')->put('requirements/document.pdf', 'tampered document');
        UploadSecurityRecord::create($this->recordAttributes(hash('sha256', 'original document')));

        $this->assertSame(1, Artisan::call('uploads:verify-security'));

        $record = UploadSecurityRecord::firstOrFail();
        $this->assertSame('integrity_failed', $record->lifecycle_status);
        $this->assertSame('tampered document', Storage::disk('local')->get('requirements/document.pdf'));
        $this->assertStringContainsString('No files were modified or deleted', Artisan::output());
    }

    public function test_integrity_command_verifies_an_unchanged_file(): void
    {
        Storage::disk('local')->put('requirements/document.pdf', 'original document');
        UploadSecurityRecord::create($this->recordAttributes(hash('sha256', 'original document')));

        $this->assertSame(0, Artisan::call('uploads:verify-security'));
        $this->assertSame('active', UploadSecurityRecord::firstOrFail()->lifecycle_status);
    }

    public function test_it_rejects_a_different_checksum_for_an_existing_storage_path(): void
    {
        $recorder = new UploadSecurityRecorder;
        $recorder->recordStored(
            'requirements/document.pdf',
            'document.pdf',
            'requirements',
            'pdf',
            new SecureUploadInspection('pdf', 'application/pdf', 8, hash('sha256', 'original'), 'disabled'),
            'new_upload'
        );

        $this->expectException(\RuntimeException::class);
        $recorder->recordStored(
            'requirements/document.pdf',
            'document.pdf',
            'requirements',
            'pdf',
            new SecureUploadInspection('pdf', 'application/pdf', 7, hash('sha256', 'changed'), 'disabled'),
            'legacy_migration'
        );
    }

    private function recordAttributes(string $sha256): array
    {
        return [
            'storage_disk' => 'local',
            'storage_path' => 'requirements/document.pdf',
            'filename' => 'document.pdf',
            'directory' => 'requirements',
            'profile' => 'pdf',
            'extension' => 'pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => 17,
            'sha256' => $sha256,
            'validation_status' => 'validated',
            'scanner_driver' => 'none',
            'scan_status' => 'disabled',
            'hosting_scanner' => 'monarx',
            'hosting_scan_status' => 'provider_managed',
            'origin' => 'new_upload',
            'lifecycle_status' => 'active',
            'last_verified_at' => now(),
        ];
    }
}
