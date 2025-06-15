<?php
namespace App\Service;

class FileUploader
{
    public function delete(string $filename): void
    {
        if (file_exists($filename)) {
            unlink($filename);
        }
    }
}
