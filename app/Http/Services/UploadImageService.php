<?php

namespace App\Http\Services;

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class UploadImageService
{
    protected $maxWidth = 800;

    protected $maxHeight = 600;

    protected const DEFAULT_DISK = 'public';

    /**
     * Upload a single image file.
     *
     * @param  string  $storagePath
     * @return string|null
     */
    public function uploadImage(UploadedFile $file, $storagePath = 'stores', string $disk = self::DEFAULT_DISK)
    {
        return $this->uploadSingleImage($file, $storagePath, $disk);
    }

    /**
     * Summary of uploadImages
     *
     * @param  mixed  $storagePath
     * @param  mixed  $currentCount
     * @return array<array{file_name: string, file_path: string, file_size: bool|int, file_type: string, order: mixed|null>}
     */
    public function uploadImages(array $images, $storagePath = 'stores', $currentCount = 0, string $disk = self::DEFAULT_DISK)
    {
        if (! $images) {
            return [];
        }

        return collect($images)
            ->map(function ($image, $index) use ($storagePath, $currentCount, $disk) {
                if (isset($image) && $image instanceof UploadedFile) {
                    $uploadedImagePath = $this->uploadSingleImage($image, $storagePath, $disk);

                    if ($uploadedImagePath) {
                        return [
                            'file_path' => $uploadedImagePath,
                            'file_name' => $image->getClientOriginalName(),
                            'file_type' => $image->getClientMimeType(),
                            'file_size' => $image->getSize(),
                            'order' => $currentCount + $index,
                        ];
                    }
                }

                return null;
            })
            ->filter()
            ->values()
            ->all();
    }

    public function uploadImageFromUrl(string $url, $storagePath = 'stores', string $disk = self::DEFAULT_DISK)
    {
        return $this->uploadSingleImageFromUrl($url, $storagePath, $disk);
    }

    public function uploadImagesFromUrl(array $urls, $storagePath = 'stores', string $disk = self::DEFAULT_DISK)
    {
        return collect($urls)
            ->map(function ($url) use ($storagePath, $disk) {
                return $this->uploadSingleImageFromUrl($url, $storagePath, $disk);
            })
            ->filter()
            ->values()
            ->all();
    }

    protected function uploadSingleImageFromUrl(string $url, $storagePath = 'stores', string $disk = self::DEFAULT_DISK)
    {
        try {
            $fileName = Str::uuid() . '.jpg';
            $path = $this->createPath($storagePath) . '/' . $fileName;
            Storage::disk($disk)->put($path, file_get_contents($url));

            return $path;
        } catch (\Exception $e) {
            Log::error('Failed to upload image from URL: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Summary of copyImage
     *
     * @param  mixed  $path
     * @param  mixed  $originalKey
     * @return string|null
     */
    public function copyImage($path, $originalKey, string $disk = self::DEFAULT_DISK)
    {
        try {
            $storage = Storage::disk($disk);
            $currentDate = now()->format('Y-m');
            $newKey = $path . '/' . $currentDate . '/' . uniqid() . '_' . basename($originalKey);
            if ($storage->exists($originalKey)) {
                $contents = $storage->get($originalKey);
                $storage->put($newKey, $contents);

                return $newKey;
            }
        } catch (\Exception $e) {
        }

        return null;
    }

    /**
     * Summary of deleteImages
     *
     * @return void
     */
    public function deleteImages(array $images, string $disk = self::DEFAULT_DISK)
    {
        foreach ($images as $image) {
            $this->deleteImage($image['file_path'], $disk);
        }
    }

    public function deleteImagesByPath(array $paths, string $disk = self::DEFAULT_DISK)
    {
        foreach ($paths as $path) {
            $this->deleteImage($path, $disk);
        }
    }

    /**
     * Summary of deleteImage
     *
     * @return bool
     */
    public function deleteImage(string $filePath, string $disk = self::DEFAULT_DISK)
    {
        if (! $filePath) {
            return false;
        }

        try {
            if (Storage::disk($disk)->exists($filePath)) {
                Storage::disk($disk)->delete($filePath);

                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }

    /**
     * Upload a single image file without resizing.
     *
     * @param  string  $path
     * @return string|null
     */
    public function uploadImageWithoutResize(UploadedFile $file, $path = 'stores', string $disk = self::DEFAULT_DISK)
    {
        if (! $file instanceof UploadedFile) {
            return null;
        }

        $fileName = $this->generateUniqueFileName($file);
        $currentDate = now()->format('Y-m');
        $filePath = "{$path}/{$currentDate}/{$fileName}";

        try {
            Storage::disk($disk)->put($filePath, file_get_contents($file));

            return $filePath;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Summary of generateUniqueFileName
     *
     * @return string
     */
    protected function generateUniqueFileName(UploadedFile $file)
    {
        return Str::uuid() . '.' . $file->getClientOriginalExtension();
    }

    /**
     * Summary of resizeImage
     */
    protected function resizeImage(UploadedFile $file)
    {
        $img = Image::read($file);

        if ($img->width() > $this->maxWidth || $img->height() > $this->maxHeight) {
            $img->resize($this->maxWidth, $this->maxHeight, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        return $img;
    }

    /**
     * Summary of uploadFile
     *
     * @param  mixed  $tempPath
     * @param  mixed  $storagePath
     * @param  mixed  $fileName
     * @return string|null
     */
    protected function uploadFile($tempPath, $storagePath, $fileName, string $disk = self::DEFAULT_DISK)
    {
        try {
            $currentDate = now()->format('Y-m');
            $filePath = "{$storagePath}/{$currentDate}/{$fileName}";

            $uploaded = Storage::disk($disk)->putFileAs(
                "{$storagePath}/{$currentDate}",
                new File($tempPath),
                $fileName,
                'public'
            );

            return $uploaded ? $filePath : null;
        } catch (\Exception $e) {
            Log::error('Disk upload failed: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Summary of deleteTempFile
     *
     * @param  mixed  $tempPath
     * @return void
     */
    protected function deleteTempFile($tempPath)
    {
        if (file_exists($tempPath)) {
            unlink($tempPath);
        }
    }

    /**
     * Summary of uploadWithStream
     *
     * @param  mixed  $storagePath
     * @return string|null
     */
    public function uploadWithStream(UploadedFile $file, $storagePath = 'videos', string $disk = self::DEFAULT_DISK)
    {
        if (! $file instanceof UploadedFile) {
            return null;
        }

        $fileName = $this->generateUniqueFileName($file);
        $currentDate = now()->format('Y-m');
        $filePath = "{$storagePath}/{$currentDate}/{$fileName}";

        try {
            $stream = fopen($file->getRealPath(), 'r');
            Storage::disk($disk)->writeStream($filePath, $stream);
            if (is_resource($stream)) {
                fclose($stream);
            }

            return $filePath;
        } catch (\Exception $e) {
            Log::error('File upload failed: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Summary of getAspectRatio
     *
     * @param  mixed  $file
     * @return string
     */
    public function getAspectRatio($file)
    {
        $img = Image::read($file);
        $width = $img->width();
        $height = $img->height();

        $gcd = $this->gcd($width, $height);

        $aspectRatio = ($width / $gcd) . ':' . ($height / $gcd);

        return $aspectRatio;
    }

    /**
     * Summary of uploadSingleImage
     *
     * @param  mixed  $storagePath
     * @return string|null
     */
    protected function uploadSingleImage(UploadedFile $file, $storagePath = 'stores', string $disk = self::DEFAULT_DISK)
    {
        try {
            $fileName = $this->generateUniqueFileName($file);
            $currentDate = now()->format('Y-m');
            $directory = $this->createPath($storagePath);
            $uploaded = Storage::disk($disk)->putFileAs(
                $directory,
                $file,
                $fileName,
            );

            return $uploaded ? "{$directory}/{$fileName}" : null;
        } catch (\Exception $e) {
            Log::error('Failed to upload image to disk', [
                'error' => $e->getMessage(),
                'file' => $fileName ?? null,
                'original_name' => $file->getClientOriginalName(),
            ]);

            return null;
        }
    }

    public function createPath($storagePath)
    {
        $currentDate = now()->format('Y-m');

        return "{$storagePath}/{$currentDate}";
    }

    /**
     * Summary of gcd
     *
     * @param  mixed  $width
     * @param  mixed  $height
     */
    private function gcd($width, $height)
    {
        while ($height) {
            $temp = $height;
            $height = $width % $height;
            $width = $temp;
        }

        return $width;
    }
}
