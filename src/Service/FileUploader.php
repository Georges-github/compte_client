<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Mime\MimeTypes;

class FileUploader
{
    private string $targetDirectory;
    private SluggerInterface $slugger;

    private array $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'docx'];
    private int $maxFileSize = 5 * 1024 * 1024; // 5 Mo

    public function __construct(string $targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = rtrim($targetDirectory, '/');
        $this->slugger = $slugger;
    }

    // public function upload(UploadedFile $file, string $subdirectory = '', ?string $oldFilename = null, bool $generateThumbnail = false): string
    public function upload(
        UploadedFile $file,
        string $subdirectory = '',
        ?string $oldFilename = null,
        bool $generateThumbnail = false,
        ?array $thumbnailSize = null
    ): string
        {
        $this->validate($file);

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $extension = $file->guessExtension() ?? 'bin';
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $extension;

        $destination = $this->getTargetDirectory($subdirectory);

        try {
            $file->move($destination, $newFilename);

            // Supprimer l'ancien fichier si présent
            if ($oldFilename) {
                $this->delete(($subdirectory ? $subdirectory . '/' : '') . $oldFilename);
            }

            // Générer une miniature si demandé
            // if ($generateThumbnail && in_array(strtolower($extension), ['jpg', 'jpeg', 'png'])) {
            //     $this->generateThumbnail($destination . '/' . $newFilename, $destination . '/thumb_' . $newFilename);
            // }
            if ($generateThumbnail && in_array(strtolower($extension), ['jpg', 'jpeg', 'png'])) {
                [$thumbWidth, $thumbHeight] = $thumbnailSize ?? [200, 200];
                $this->generateThumbnail(
                    $destination . '/' . $newFilename,
                    $destination . '/thumb_' . $newFilename,
                    $thumbWidth,
                    $thumbHeight
                );
            }
            
        } catch (FileException $e) {
            throw new \RuntimeException('Échec de l’envoi du fichier : ' . $e->getMessage());
        }

        return ($subdirectory ? $subdirectory . '/' : '') . $newFilename;
    }

    public function delete(string $relativePath): bool
    {
        $fullPath = $this->targetDirectory . '/' . $relativePath;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    public function validate(UploadedFile $file): void
    {
        if ($file->getSize() > $this->maxFileSize) {
            throw new \RuntimeException('Fichier trop volumineux. Maximum autorisé : ' . $this->maxFileSize / 1024 / 1024 . ' Mo');
        }

        // $extension = $file->getClientOriginalExtension();
        // // $extension = $file->guessExtension();
        // if (!in_array($extension, $this->allowedExtensions)) {
        //     throw new \RuntimeException('Extension de fichier non autorisée : ' . $extension);
        // }
    }

    private function getTargetDirectory(string $subdirectory = ''): string
    {
        $path = $this->targetDirectory;
        if ($subdirectory) {
            $path .= '/' . trim($subdirectory, '/');
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }
        return $path;
    }

    private function generateThumbnail(string $sourcePath, string $thumbPath, int $width = 200, int $height = 200): void
    {
        [$srcWidth, $srcHeight, $type] = getimagesize($sourcePath);

        switch ($type) {
            case IMAGETYPE_JPEG:
                $srcImage = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $srcImage = imagecreatefrompng($sourcePath);
                break;
            default:
                throw new \RuntimeException('Type d\'image non supporté pour la miniature.');
        }

        $thumbImage = imagecreatetruecolor($width, $height);

        // Resize et crop (centré)
        $aspectRatioSrc = $srcWidth / $srcHeight;
        $aspectRatioThumb = $width / $height;

        if ($aspectRatioSrc > $aspectRatioThumb) {
            // Image plus large
            $newHeight = $height;
            $newWidth = intval($height * $aspectRatioSrc);
        } else {
            // Image plus haute
            $newWidth = $width;
            $newHeight = intval($width / $aspectRatioSrc);
        }

        $tempImage = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($tempImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $srcWidth, $srcHeight);

        // Crop au centre
        $x = intval(($newWidth - $width) / 2);
        $y = intval(($newHeight - $height) / 2);
        imagecopy($thumbImage, $tempImage, 0, 0, $x, $y, $width, $height);

        imagejpeg($thumbImage, $thumbPath, 90);

        imagedestroy($srcImage);
        imagedestroy($thumbImage);
        imagedestroy($tempImage);
    }
}
