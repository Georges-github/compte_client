<?php
namespace App\Tests\Fake;

use App\Service\FileUploader;

class FakeFileUploader extends FileUploader
{
    private array $deletedFiles = [];

    public function delete(string $filename): void
    {
        $this->deletedFiles[] = $filename;
    }

    public function getDeletedFiles(): array
    {
        return $this->deletedFiles;
    }
}
