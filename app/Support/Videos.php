<?php

namespace App\Support;

/**
 * Resolves video files stored under public/videos/ into public URLs.
 *
 * Clips are usually too large to sit next to the photos, and a hero video may
 * just as well be served from a CDN, so this mirrors Images with its own base
 * folder and accepts a full URL as-is.
 */
class Videos extends Media
{
    protected static function base(): string
    {
        return (string) config('school.videos.base', 'videos');
    }

    protected static function folder(string $name): ?string
    {
        return config('school.videos.folders.'.$name);
    }
}
