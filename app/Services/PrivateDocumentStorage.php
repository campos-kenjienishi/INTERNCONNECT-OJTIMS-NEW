<?php

namespace App\Services;

use App\Exceptions\UnsafeUploadException;
use Illuminate\Support\Facades\Storage;

class PrivateDocumentStorage
{
    public const REQUIREMENTS = 'requirements';

    public const MOA = 'moa';

    public const TEMPLATES = 'templates';

    public function __construct(private readonly ?UploadSecurityRecorder $securityRecorder = null) {}

    public function resolve(string $directory, string $filename): ?string
    {
        $filename = $this->safeFilename($filename);
        $securePath = $this->relativePath($directory, $filename);
        $disk = Storage::disk((string) config('upload_security.disk'));

        if ($disk->exists($securePath)) {
            return $disk->path($securePath);
        }

        $legacyPath = $this->legacyPath($filename);

        return is_file($legacyPath) ? $legacyPath : null;
    }

    public function exists(string $directory, string $filename): bool
    {
        return $this->resolve($directory, $filename) !== null;
    }

    public function size(string $directory, string $filename): int
    {
        $path = $this->resolve($directory, $filename);

        if ($path === null) {
            return 0;
        }

        $size = filesize($path);

        return $size === false ? 0 : $size;
    }

    public function delete(string $directory, string $filename): void
    {
        $filename = $this->safeFilename($filename);
        $securePath = $this->relativePath($directory, $filename);
        $disk = Storage::disk((string) config('upload_security.disk'));

        $secureFileExisted = $disk->exists($securePath);

        if ($secureFileExisted) {
            $disk->delete($securePath);
        }

        $legacyPath = $this->legacyPath($filename);

        if (is_file($legacyPath)) {
            @unlink($legacyPath);
        }

        if ($secureFileExisted) {
            ($this->securityRecorder ?? app(UploadSecurityRecorder::class))->retire($directory, $filename);
        }
    }

    public function requirementDirectory(?string $requirementName): string
    {
        return trim((string) $requirementName) === 'Notarized MOA'
            ? self::MOA
            : self::REQUIREMENTS;
    }

    private function relativePath(string $directory, string $filename): string
    {
        $directory = trim(str_replace('\\', '/', $directory), '/');

        if (preg_match('#^[A-Za-z0-9_-]+(?:/[A-Za-z0-9_-]+)*$#', $directory) !== 1) {
            throw new UnsafeUploadException('invalid_destination', 'The document storage destination is invalid.');
        }

        return $directory.'/'.$filename;
    }

    private function safeFilename(string $filename): string
    {
        $filename = trim($filename);

        if ($filename === '' || basename(str_replace('\\', '/', $filename)) !== $filename) {
            throw new UnsafeUploadException('invalid_filename', 'The stored document filename is invalid.');
        }

        return $filename;
    }

    private function legacyPath(string $filename): string
    {
        return rtrim((string) config('upload_security.legacy_directory'), '/\\').DIRECTORY_SEPARATOR.$filename;
    }
}
