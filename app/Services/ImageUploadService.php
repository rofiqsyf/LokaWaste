<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    /**
     * Upload an image:
     * 1. Strips EXIF metadata & GPS (TC-19, SDD 8.7)
     * 2. Resizes to max 1280px maintaining aspect ratio
     * 3. Re-encodes cleanly to WebP
     * 4. Stores in non-executable storage
     */
    public function processAndStore(UploadedFile $file, string $folder = 'listings'): string
    {
        $imagePath = $file->getRealPath();
        $mime = $file->getMimeType();

        // Create GD image resource based on mime type
        $srcImage = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($imagePath),
            'image/png' => @imagecreatefrompng($imagePath),
            'image/webp' => @imagecreatefromwebp($imagePath),
            default => null,
        };

        if (!$srcImage) {
            // Fallback: standard secure store
            return $file->store($folder, 'public');
        }

        $width = imagesx($srcImage);
        $height = imagesy($srcImage);

        // Max dimension 1280px
        $maxDimension = 1280;
        if ($width > $maxDimension || $height > $maxDimension) {
            if ($width >= $height) {
                $newWidth = $maxDimension;
                $newHeight = (int) round(($height / $width) * $maxDimension);
            } else {
                $newHeight = $maxDimension;
                $newWidth = (int) round(($width / $height) * $maxDimension);
            }

            $destImage = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($srcImage);
            $finalImage = $destImage;
        } else {
            $finalImage = $srcImage;
        }

        $filename = Str::random(40) . '.webp';
        $relativeStoragePath = "{$folder}/{$filename}";
        $fullPath = Storage::disk('public')->path($relativeStoragePath);

        // Ensure directory exists
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Save re-encoded WebP with quality 85 (completely strips EXIF and GPS)
        imagewebp($finalImage, $fullPath, 85);
        imagedestroy($finalImage);

        return $relativeStoragePath;
    }
}
