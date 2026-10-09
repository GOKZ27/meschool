<?php

namespace App\Support;

/**
 * Resolves photos stored under public/images/ into public URLs.
 *
 * Keeping the path building in one place means a view never has to hard-code
 * the "images/" prefix, so moving a folder later is a config change rather
 * than a search across every Blade file.
 */
class Images extends Media
{
    protected static function base(): string
    {
        return (string) config('school.images.base', 'images');
    }

    protected static function folder(string $name): ?string
    {
        return config('school.images.folders.'.$name);
    }
}
