<?php

namespace Tests\Unit;

use App\Exceptions\UnsafeUploadException;
use App\Models\UploadSecurityRecord;
use App\Services\SecureUploadService;
use App\Services\Uploads\MalwareScanner;
use App\Services\Uploads\MalwareScannerStatus;
use App\Services\Uploads\MalwareScanResult;
use App\Services\Uploads\NullMalwareScanner;
use App\Services\Uploads\RejectingMalwareScanner;
use App\Services\UploadSecurityRecorder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class SecureUploadServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        config()->set('upload_security.disk', 'local');
        config()->set('upload_security.storage_directory', 'private/uploads');
        config()->set('upload_security.max_bytes', 30 * 1024 * 1024);
        config()->set('upload_security.profiles.pdf.max_bytes', 30 * 1024 * 1024);
        config()->set('upload_security.evidence_enabled', false);
    }

    public function test_it_stores_a_valid_pdf_under_a_generated_private_name(): void
    {
        $upload = UploadedFile::fake()->createWithContent('student-requirement.pdf', $this->validPdf());

        $result = $this->service()->store($upload, 'pdf', 'requirements');

        Storage::disk('local')->assertExists($result->path);
        $this->assertQuarantineIsEmpty();
        $this->assertStringStartsWith('private/uploads/requirements/', $result->path);
        $this->assertNotSame('student-requirement.pdf', $result->filename);
        $this->assertMatchesRegularExpression('/^[0-9a-f-]{36}\.pdf$/', $result->filename);
        $this->assertSame('application/pdf', $result->mimeType);
        $this->assertSame(hash('sha256', $this->validPdf()), $result->sha256);
        $this->assertSame('disabled', $result->scanStatus);
    }

    public function test_it_records_security_evidence_for_an_accepted_upload(): void
    {
        config()->set('upload_security.evidence_enabled', true);
        $record = new UploadSecurityRecord;
        $record->setAttribute('id', 321);
        $record->exists = true;
        $recorder = \Mockery::mock(UploadSecurityRecorder::class);
        $recorder->shouldReceive('recordStored')->once()->andReturn($record);
        $upload = UploadedFile::fake()->createWithContent('student-requirement.pdf', $this->validPdf());

        $result = (new SecureUploadService(new NullMalwareScanner, $recorder))
            ->store($upload, 'pdf', 'requirements');

        $this->assertSame(321, $result->securityRecordId);
        Storage::disk('local')->assertExists($result->path);
    }

    public function test_it_rejects_and_removes_an_upload_when_evidence_cannot_be_recorded(): void
    {
        config()->set('upload_security.evidence_enabled', true);
        $recorder = \Mockery::mock(UploadSecurityRecorder::class);
        $recorder->shouldReceive('recordStored')->once()->andThrow(new \RuntimeException('database unavailable'));
        $upload = UploadedFile::fake()->createWithContent('student-requirement.pdf', $this->validPdf());
        $service = new SecureUploadService(new NullMalwareScanner, $recorder);

        $this->assertRejected('security_record_failed', fn () => $service->store($upload, 'pdf', 'requirements'));
        $this->assertQuarantineIsEmpty();
        $this->assertSame([], Storage::disk('local')->allFiles('private/uploads/requirements'));
    }

    public function test_it_rejects_php_content_renamed_as_a_pdf(): void
    {
        $upload = UploadedFile::fake()->createWithContent('requirement.pdf', '<?php echo "unsafe";');

        $exception = $this->assertRejected('mime_mismatch', fn () => $this->service()->store($upload, 'pdf', 'requirements'));
        $this->assertStringContainsString('Renaming a different file type to .pdf is not allowed.', $exception->getMessage());
        $this->assertQuarantineIsEmpty();
    }

    public function test_it_rejects_a_pdf_without_an_end_marker(): void
    {
        $upload = UploadedFile::fake()->createWithContent('broken.pdf', "%PDF-1.4\n1 0 obj\n<<>>\nendobj\n");

        $this->assertRejected('invalid_pdf', fn () => $this->service()->store($upload, 'pdf', 'requirements'));
        $this->assertQuarantineIsEmpty();
    }

    public function test_it_rejects_files_over_the_profile_limit_before_storage(): void
    {
        config()->set('upload_security.profiles.pdf.max_bytes', 10);
        $upload = UploadedFile::fake()->createWithContent('large.pdf', $this->validPdf());

        $this->assertRejected('file_too_large', fn () => $this->service()->store($upload, 'pdf', 'requirements'));
        $this->assertQuarantineIsEmpty();
    }

    public function test_it_deletes_quarantined_files_when_malware_is_detected(): void
    {
        $scanner = new class implements MalwareScanner
        {
            public function scan(string $path): MalwareScanResult
            {
                return MalwareScanResult::infected('Test.Signature');
            }

            public function status(): MalwareScannerStatus
            {
                return MalwareScannerStatus::ready();
            }
        };

        $upload = UploadedFile::fake()->createWithContent('infected.pdf', $this->validPdf());

        $this->assertRejected('malware_detected', fn () => $this->service($scanner)->store($upload, 'pdf', 'requirements'));
        $this->assertQuarantineIsEmpty();
    }

    public function test_it_deletes_quarantined_files_when_required_scanning_is_not_configured(): void
    {
        $upload = UploadedFile::fake()->createWithContent('requirement.pdf', $this->validPdf());

        $this->assertRejected(
            'scanner_required',
            fn () => $this->service(new RejectingMalwareScanner)->store($upload, 'pdf', 'requirements')
        );
        $this->assertQuarantineIsEmpty();
    }

    public function test_it_rejects_unsafe_destination_paths(): void
    {
        $upload = UploadedFile::fake()->createWithContent('valid.pdf', $this->validPdf());

        $this->assertRejected('invalid_destination', fn () => $this->service()->store($upload, 'pdf', '../public'));
        $this->assertQuarantineIsEmpty();
    }

    public function test_it_accepts_a_structurally_valid_docx_template(): void
    {
        $upload = $this->openXmlUpload('docx', 'word/document.xml');

        try {
            $result = $this->service()->store($upload, 'template', 'templates');

            Storage::disk('local')->assertExists($result->path);
            $this->assertSame('docx', $result->extension);
            $this->assertQuarantineIsEmpty();
        } finally {
            @unlink($upload->getPathname());
        }
    }

    public function test_it_rejects_an_xlsx_extension_on_a_word_document(): void
    {
        $upload = $this->openXmlUpload('xlsx', 'word/document.xml');

        try {
            $this->assertRejected('invalid_office_document', fn () => $this->service()->store($upload, 'template', 'templates'));
            $this->assertQuarantineIsEmpty();
        } finally {
            @unlink($upload->getPathname());
        }
    }

    private function service(?MalwareScanner $scanner = null): SecureUploadService
    {
        return new SecureUploadService($scanner ?? new NullMalwareScanner);
    }

    private function validPdf(): string
    {
        return "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n<< /Root 1 0 R >>\n%%EOF\n";
    }

    private function openXmlUpload(string $extension, string $documentEntry): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'secure-upload-test-');
        $archive = new ZipArchive;
        $archive->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $archive->addFromString('[Content_Types].xml', '<Types></Types>');
        $archive->addFromString($documentEntry, '<document></document>');
        $archive->close();

        return new UploadedFile($path, "template.{$extension}", null, UPLOAD_ERR_OK, true);
    }

    private function assertRejected(string $reason, callable $operation): UnsafeUploadException
    {
        try {
            $operation();
            $this->fail("Expected upload rejection: {$reason}");
        } catch (UnsafeUploadException $exception) {
            $this->assertSame($reason, $exception->reason);

            return $exception;
        }
    }

    private function assertQuarantineIsEmpty(): void
    {
        $this->assertSame([], Storage::disk('local')->allFiles('private/uploads/quarantine'));
    }
}
