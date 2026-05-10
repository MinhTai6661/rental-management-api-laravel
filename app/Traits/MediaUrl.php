<?php

namespace App\Traits;

use Storage;

trait MediaUrl
{
    /**
     * Get the full S3 URL for a given path.
     *
     * @param  string  $path
     * @return string
     */
    public function getMediaUrl($path)
    {
        if (empty($path)) {
            return null;
        }
        if (str_starts_with($path, 'http')) {
            return $path;
        }
        return asset(Storage::url($path));
    }
}
