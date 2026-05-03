<?php

namespace App\Traits;

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

trait UploadImage
{
    protected $maxWidth = 800;

    protected $maxHeight = 600;

    /**
     * Handle the images upload process.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $storagePath
     * @return array
     */
    public function uploadImagesWithOrder($request, $storagePath = 'stores')
    {
        if (! $request->has('images')) {
            return [];
        }

        return collect($request->images)
            ->map(function ($image, $index) use ($storagePath) {
                if (isset($image['image_file']) && $image['image_file'] instanceof UploadedFile) {
                    $uploadedPath = $this->uploadSingleImageToS3($image['image_file'], $storagePath);

                    if ($uploadedPath) {
                        return [
                            'image_key' => $uploadedPath,
                            'order' => $image['order'] ?? $index + 1,
                        ];
                    }
                }

                return null;
            })
            ->filter()
            ->values()
            ->all();
    }

    public function copyS3($path, $originalKey)
    {
        try {
            $s3 = Storage::disk('s3');
            $currentDate = now()->format('Y-m');
            $newKey = $path.'/'.$currentDate.'/'.uniqid().'_'.basename($originalKey);
            if ($s3->exists($originalKey)) {
                // $s3->copy($originalKey, $newKey);
                $contents = $s3->get($originalKey);
                $s3->put($newKey, $contents);

                return $newKey;
            }
        } catch (\Exception $e) {
        }

        return null;
    }

    public function deleteS3($filePath)
    {

        if (! $filePath) {
            return false;
        }

        try {
            if (Storage::disk('s3')->exists($filePath)) {
                Storage::disk('s3')->delete($filePath);

                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }

    /**
     * Upload a single image file.
     *
     * @param  string  $storagePath
     * @return string|null
     */
    public function uploadImage(UploadedFile $file, $storagePath = 'stores')
    {
        return $this->uploadSingleImageToS3($file, $storagePath);
    }

    /**
     * Upload a single image file.
     *
     * @param  string  $storagePath
     * @return string|null
     */
    public function uploadImageWithoutResize(UploadedFile $file, $path = 'stores')
    {
        if (! $file instanceof UploadedFile) {
            return null;
        }

        $filename = $this->generateUniqueFilename($file);
        $currentDate = now()->format('Y-m');
        $s3Path = "{$path}/{$currentDate}/{$filename}";

        try {
            Storage::disk('s3')->put($s3Path, file_get_contents($file));

            return $s3Path;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Process and upload an image.
     *
     * @param  string  $storagePath
     * @return string|null
     */
    protected function processAndUploadImage(UploadedFile $file, $storagePath)
    {
        $filename = $this->generateUniqueFilename($file);
        $img = $this->resizeImage($file);
        $tempPath = $this->saveTempFile($img, $filename);

        try {
            return $this->uploadToS3($tempPath, $storagePath, $filename);
        } catch (\Exception $e) {
            return null;
        } finally {
            $this->deleteTempFile($tempPath);
        }
    }

    /**
     * Generate a unique filename for the image.
     *
     * @return string
     */
    protected function generateUniqueFilename(UploadedFile $file)
    {
        return Str::uuid().'.'.$file->getClientOriginalExtension();
    }

    /**
     * Resize the image if necessary.
     *
     * @return \Intervention\Image\Image
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
     * Save the image to a temporary file.
     *
     * @param  \Intervention\Image\Image  $img
     * @param  string  $filename
     * @return string
     */
    protected function saveTempFile($img, $filename)
    {
        $tempPath = storage_path('app/tmp/'.$filename);
        $img->save($tempPath);

        return $tempPath;
    }

    /**
     * Upload the file to S3.
     *
     * @param  string  $tempPath
     * @param  string  $storagePath
     * @param  string  $filename
     * @return string|null
     */
    protected function uploadToS3($tempPath, $storagePath, $filename)
    {
        try {
            $currentDate = now()->format('Y-m');
            $s3Path = "{$storagePath}/{$currentDate}/{$filename}";

            $uploaded = Storage::disk('s3')->putFileAs(
                "{$storagePath}/{$currentDate}",
                new File($tempPath),
                $filename,
                'public'
            );

            return $uploaded ? $s3Path : null;
        } catch (\Exception $e) {
            Log::error('S3 upload failed: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Delete the temporary file.
     *
     * @param  string  $tempPath
     * @return void
     */
    protected function deleteTempFile($tempPath)
    {
        if (file_exists($tempPath)) {
            unlink($tempPath);
        }
    }

    public function uploadWithStream(UploadedFile $file, $storagePath = 'videos')
    {
        if (! $file instanceof UploadedFile) {
            return null;
        }

        $filename = $this->generateUniqueFilename($file);
        $currentDate = now()->format('Y-m');
        $s3Path = "{$storagePath}/{$currentDate}/{$filename}";

        try {
            $stream = fopen($file->getRealPath(), 'r');
            Storage::disk('s3')->writeStream($s3Path, $stream);
            if (is_resource($stream)) {
                fclose($stream);
            }

            return $s3Path;
        } catch (\Exception $e) {
            Log::error('Video upload failed: '.$e->getMessage());

            return null;
        }
    }

    public function getAspectRatio($file)
    {
        $img = Image::read($file);
        $width = $img->width();
        $height = $img->height();

        $gcd = $this->gcd($width, $height);

        $aspectRatio = ($width / $gcd).':'.($height / $gcd);

        return $aspectRatio;
    }

    protected function uploadSingleImageToS3(UploadedFile $file, $storagePath = 'stores')
    {
        try {
            $filename = $this->generateUniqueFilename($file);
            $currentDate = now()->format('Y-m');
            $directory = "{$storagePath}/{$currentDate}";

            $uploaded = Storage::disk('s3')->putFileAs(
                $directory,
                $file,
                $filename,
            );

            return $uploaded ? "{$directory}/{$filename}" : null;
        } catch (\Exception $e) {
            Log::error('Failed to upload image to S3', [
                'error' => $e->getMessage(),
                'file' => $filename ?? null,
                'original_name' => $file->getClientOriginalName(),
            ]);

            return null;
        }
    }

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
