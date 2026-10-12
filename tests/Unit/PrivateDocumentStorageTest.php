<?php

namespace Tests\Unit;

use App\Exceptions\UnsafeUploadException;
use App\Services\PrivateDocumentStorage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class PrivateDocumentStorageTest extends TestCase
{
    private string $legacyDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('secure_uploads');
        config()->set('upload_security.disk', 'secure_uploads');
        $this->legacyDirectory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'internconnect-legacy-'.Str::uuid();
        mkdir($this->legacyDirectory);
        config()->set('upload_security.legacy_directory', $this->legacyDirectory);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->legacyDirectory.DIRECTORY_SEPARATOR.'*') ?: [] as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        if (is_dir($this->legacyDirectory)) {
            rmdir($this->legacyDirectory);
        }

        parent::tearDown();
    }

    public function test_it_resolves_files_from_private_storage(): void
    {
        Storage::disk('secure_uploads')->put('requirements/private.pdf', 'private document');

        $path = $this->storage()->resolve(PrivateDocumentStorage::REQUIREMENTS, 'private.pdf');

        $this->assertNotNull($path);
        $this->assertSame('private document', file_get_contents($path));
        $this->assertSame(16, $this->storage()->size(PrivateDocumentStorage::REQUIREMENTS, 'private.pdf'));
    }

    public function test_it_falls_back_to_legacy_public_files(): void
    {
        $legacyPath = $this->legacyDirectory.DIRECTORY_SEPARATOR.'legacy.pdf';
        file_put_contents($legacyPath, 'legacy document');

        $this->assertSame(
            $legacyPath,
            $this->storage()->resolve(PrivateDocumentStorage::MOA, 'legacy.pdf')
        );
    }

    public function test_it_deletes_private_and_legacy_copies(): void
    {
        Storage::disk('secure_uploads')->put('templates/shared.pdf', 'private');
        $legacyPath = $this->legacyDirectory.DIRECTORY_SEPARATOR.'shared.pdf';
        file_put_contents($legacyPath, 'legacy');

        $this->storage()->delete(PrivateDocumentStorage::TEMPLATES, 'shared.pdf');

        Storage::disk('secure_uploads')->assertMissing('templates/shared.pdf');
        $this->assertFileDoesNotExist($legacyPath);
    }

    public function test_it_rejects_path_traversal_in_stored_filenames(): void
    {
        $this->expectException(UnsafeUploadException::class);

        $this->storage()->resolve(PrivateDocumentStorage::REQUIREMENTS, '../secret.pdf');
    }

    public function test_it_maps_notarized_moa_requirements_to_moa_storage(): void
    {
        $this->assertSame(
            PrivateDocumentStorage::MOA,
            $this->storage()->requirementDirectory('Notarized MOA')
        );
        $this->assertSame(
            PrivateDocumentStorage::REQUIREMENTS,
            $this->storage()->requirementDirectory('Weekly Report')
        );
    }

    private function storage(): PrivateDocumentStorage
    {
        return new PrivateDocumentStorage;
    }
}
