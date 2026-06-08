<?php

namespace Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadSecurityTest extends TestCase
{
    public function test_valid_pdf_upload_succeeds(): void
    {
        $this->replacePrivateDiskWithTempDisk();

        $response = $this->postJson('/labs/file-upload-security/secure', [
            'document' => UploadedFile::fake()->create('invoice.pdf', 256, 'application/pdf'),
        ]);

        $storedDocumentPath = $response->json('path');

        $response
            ->assertCreated()
            ->assertJsonPath('filename', basename($storedDocumentPath));

        Storage::disk('private')->assertExists($storedDocumentPath);
    }

    public function test_invalid_extension_rejected(): void
    {
        $this->replacePrivateDiskWithTempDisk();

        $response = $this->postJson('/labs/file-upload-security/secure', [
            'document' => UploadedFile::fake()->create('notes.txt', 32, 'text/plain'),
        ]);

        $response->assertUnprocessable();
    }

    public function test_oversized_file_rejected(): void
    {
        $this->replacePrivateDiskWithTempDisk();

        $response = $this->postJson('/labs/file-upload-security/secure', [
            'document' => UploadedFile::fake()->create('large-document.pdf', 3072, 'application/pdf'),
        ]);

        $response->assertUnprocessable();
    }

    public function test_generated_filename_is_used(): void
    {
        $this->replacePrivateDiskWithTempDisk();

        $response = $this->postJson('/labs/file-upload-security/secure', [
            'document' => UploadedFile::fake()->create('client-contract.pdf', 128, 'application/pdf'),
        ]);

        $response->assertCreated();

        $this->assertStringStartsWith('documents/', $response->json('path'));
        $this->assertNotSame('client-contract.pdf', $response->json('filename'));
    }

    public function test_unauthorized_download_blocked(): void
    {
        $this->replacePrivateDiskWithTempDisk();
        Storage::disk('private')->put('documents/private-contract.pdf', 'demo');

        $response = $this->get('/labs/file-upload-security/download?path=documents/private-contract.pdf');

        $response->assertRedirect('/login');
    }

    private function replacePrivateDiskWithTempDisk(): void
    {
        $filesystem = new Filesystem();
        $privateDiskRoot = sys_get_temp_dir().DIRECTORY_SEPARATOR.'laravel-security-lab-private-test';

        $filesystem->ensureDirectoryExists($privateDiskRoot);
        $filesystem->cleanDirectory($privateDiskRoot);

        Storage::set('private', Storage::build([
            'driver' => 'local',
            'root' => $privateDiskRoot,
        ]));
    }
}
