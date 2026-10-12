<?php

namespace Tests\Unit;

use App\Services\SecureDocumentResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class SecureDocumentResponseTest extends TestCase
{
    private array $temporaryFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->temporaryFiles as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        parent::tearDown();
    }

    public function test_inline_pdf_responses_include_hardened_headers(): void
    {
        $response = $this->service()->inlinePdf($this->temporaryFile($this->validPdf()), 'Student Requirement.pdf');

        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertSame('same-origin', $response->headers->get('Cross-Origin-Resource-Policy'));
        $this->assertSame('no-referrer', $response->headers->get('Referrer-Policy'));
        $this->assertSame('SAMEORIGIN', $response->headers->get('X-Frame-Options'));
        $this->assertStringContainsString("sandbox; default-src 'none'", $response->headers->get('Content-Security-Policy'));
        $this->assertStringContainsString('inline;', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('student_requirement.pdf', $response->headers->get('Content-Disposition'));
    }

    public function test_downloads_are_forced_to_binary_attachments(): void
    {
        $response = $this->service()->download($this->temporaryFile('document body'), "Unsafe\r\nName.php");

        $this->assertSame('application/octet-stream', $response->headers->get('Content-Type'));
        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertStringContainsString('attachment;', $response->headers->get('Content-Disposition'));
        $this->assertStringNotContainsString("\r", $response->headers->get('Content-Disposition'));
        $this->assertStringNotContainsString("\n", $response->headers->get('Content-Disposition'));
    }

    public function test_non_pdf_content_cannot_be_rendered_inline(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(0);

        $this->service()->inlinePdf($this->temporaryFile('<script>alert(1)</script>'), 'fake.pdf');
    }

    private function service(): SecureDocumentResponse
    {
        return new SecureDocumentResponse;
    }

    private function temporaryFile(string $contents): string
    {
        $path = tempnam(sys_get_temp_dir(), 'document-response-test-');
        file_put_contents($path, $contents);
        $this->temporaryFiles[] = $path;

        return $path;
    }

    private function validPdf(): string
    {
        return "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n<< /Root 1 0 R >>\n%%EOF\n";
    }
}
