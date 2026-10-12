<?php

namespace App\Services;

use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class SecureDocumentResponse
{
    public function inlinePdf(string $path, string $filename): BinaryFileResponse
    {
        abort_unless($this->mimeType($path) === 'application/pdf', 422, 'The stored document is not a valid PDF file.');

        $response = response()->file($path, $this->securityHeaders([
            'Content-Type' => 'application/pdf',
            'Content-Security-Policy' => "sandbox; default-src 'none'",
        ]));

        $response->headers->set(
            'Content-Disposition',
            $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_INLINE,
                $this->safeFilename($filename, 'document.pdf')
            )
        );

        return $response;
    }

    public function download(string $path, string $filename): BinaryFileResponse
    {
        return response()->download(
            $path,
            $this->safeFilename($filename, 'document'),
            $this->securityHeaders([
                'Content-Type' => 'application/octet-stream',
                'Content-Security-Policy' => "sandbox; default-src 'none'",
            ])
        );
    }

    private function securityHeaders(array $headers): array
    {
        return array_merge([
            'Cache-Control' => 'private, no-store, no-cache, max-age=0, must-revalidate',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
            'X-Download-Options' => 'noopen',
            'Cross-Origin-Resource-Policy' => 'same-origin',
            'Referrer-Policy' => 'no-referrer',
            'X-Frame-Options' => 'SAMEORIGIN',
        ], $headers);
    }

    private function safeFilename(string $filename, string $fallback): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $baseName = Str::slug(pathinfo($filename, PATHINFO_FILENAME), '_');

        if ($baseName === '') {
            $baseName = Str::slug(pathinfo($fallback, PATHINFO_FILENAME), '_') ?: 'document';
        }

        return $extension === '' ? $baseName : $baseName.'.'.$extension;
    }

    private function mimeType(string $path): string
    {
        $fileInfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $fileInfo->file($path);

        return is_string($mimeType) ? strtolower($mimeType) : '';
    }
}
