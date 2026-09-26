<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class FileStorageService
{
    /**
     * The storage disk instance name.
     */
    protected string $disk;

    public function __construct(?string $disk = null)
    {
        $this->disk = $disk ?? (string) config('filesystems.default', 's3');
    }

    /**
     * Upload an image file with standard image validation.
     *
     * @param  int  $maxKiloBytes  (default: 5MB = 5120KB)
     * @return array{url: string, path: string, filename: string, extension: string, size: int, mime_type: string}
     */
    public function uploadImage(
        UploadedFile $file,
        string $directory = 'materials/images',
        int $maxKiloBytes = 5120
    ): array {
        $allowedExtensions = ['jpeg', 'jpg', 'png', 'webp', 'gif', 'svg'];

        return $this->uploadFile($file, $directory, $allowedExtensions, $maxKiloBytes);
    }

    /**
     * Upload a 3D model file (.glb, .gltf, .obj) for AR & Molecule 3D viewer.
     *
     * @param  int  $maxKiloBytes  (default: 50MB = 51200KB)
     * @return array{url: string, path: string, filename: string, extension: string, size: int, mime_type: string}
     */
    public function upload3DModel(
        UploadedFile $file,
        string $directory = 'molecules/models',
        int $maxKiloBytes = 51200
    ): array {
        $allowedExtensions = ['glb', 'gltf', 'obj', 'bin'];

        return $this->uploadFile($file, $directory, $allowedExtensions, $maxKiloBytes);
    }

    /**
     * Core reusable upload handler.
     *
     * @param  list<string>  $allowedExtensions
     * @return array{url: string, path: string, filename: string, extension: string, size: int, mime_type: string}
     *
     * @throws InvalidArgumentException
     */
    public function uploadFile(
        UploadedFile $file,
        string $directory,
        array $allowedExtensions = [],
        int $maxKiloBytes = 10240
    ): array {
        $extension = strtolower($file->getClientOriginalExtension());
        $fileSize = $file->getSize() ?: 0;
        $mimeType = $file->getMimeType() ?: 'application/octet-stream';

        // Check file size
        if ($fileSize > $maxKiloBytes * 1024) {
            $maxMb = round($maxKiloBytes / 1024, 1);
            throw new InvalidArgumentException("Ukuran berkas melebihi batas maksimal {$maxMb}MB.");
        }

        // Check extension if restricted
        if (! empty($allowedExtensions) && ! in_array($extension, $allowedExtensions, true)) {
            $allowedStr = strtoupper(implode(', ', $allowedExtensions));
            throw new InvalidArgumentException("Format berkas tidak didukung. Harap unggah berkas bertipe: {$allowedStr}.");
        }

        // Generate safe unique filename
        $cleanOriginalName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $uniqueFilename = $cleanOriginalName.'-'.Str::random(10).'.'.$extension;

        // Store file with public visibility
        $path = Storage::disk($this->disk)->putFileAs($directory, $file, $uniqueFilename, 'public');

        if (! $path) {
            throw new \RuntimeException('Gagal mengunggah berkas ke Object Storage.');
        }

        $url = $this->getUrl($path);

        return [
            'url' => $url,
            'path' => $path,
            'filename' => $uniqueFilename,
            'extension' => $extension,
            'size' => $fileSize,
            'mime_type' => $mimeType,
        ];
    }

    /**
     * Delete a file from storage by relative path or full URL.
     */
    public function deleteFile(?string $pathOrUrl): bool
    {
        if (empty($pathOrUrl)) {
            return false;
        }

        $path = $this->extractRelativePath($pathOrUrl);

        if (Storage::disk($this->disk)->exists($path)) {
            return Storage::disk($this->disk)->delete($path);
        }

        return false;
    }

    /**
     * Get the public URL for a stored file path.
     */
    public function getUrl(string $path): string
    {
        return Storage::disk($this->disk)->url($path);
    }

    /**
     * Extract the relative storage path from a full URL if provided.
     */
    protected function extractRelativePath(string $pathOrUrl): string
    {
        // If it's already a relative path
        if (! filter_var($pathOrUrl, FILTER_VALIDATE_URL)) {
            return ltrim($pathOrUrl, '/');
        }

        // If it's a full URL, extract path after bucket name or root
        $parsedUrl = parse_url($pathOrUrl, PHP_URL_PATH);
        $cleanPath = ltrim((string) $parsedUrl, '/');

        $bucket = (string) config('filesystems.disks.s3.bucket', 'mvar');
        if (str_starts_with($cleanPath, $bucket.'/')) {
            $cleanPath = substr($cleanPath, strlen($bucket) + 1);
        }

        return $cleanPath;
    }
}
