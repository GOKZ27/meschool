<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

/**
 * Path building for media served straight out of public/.
 *
 * Photos and clips are resolved the same way — a config-declared base folder
 * plus an optional subfolder — so the prefix lives in one place and a view
 * never has to hard-code it. Subclasses only declare their base and where the
 * folder names live in config.
 */
abstract class Media
{
    /**
     * The public folder this media type lives in, e.g. "images".
     */
    abstract protected static function base(): string;

    /**
     * Resolve a folder key such as "hero" to its configured subfolder.
     *
     * Returning null lets url() fall back to the key itself, so a folder that
     * is not listed in config can still be addressed by name.
     */
    protected static function folder(string $name): ?string
    {
        return null;
    }

    /**
     * Build the public URL for a file, e.g. url('hero/gedung.jpg').
     *
     * A leading slash on the path is accepted and stripped, so both
     * url('hero/gedung.jpg') and url('/hero/gedung.jpg') work. A path that
     * already carries the base prefix ("images/logo/logo.png") is not
     * doubled, so config values can be written either way.
     */
    public static function url(string $path, ?string $folder = null): string
    {
        $base = trim(static::base(), '/');
        $path = ltrim(trim($path), '/');

        if (static::isRemote($path)) {
            return $path;
        }

        if ($folder !== null) {
            $folder = trim((string) (static::folder($folder) ?? $folder), '/');
            $path = $folder.'/'.$path;
        }

        if ($base !== '' && str_starts_with($path, $base.'/')) {
            $path = substr($path, strlen($base) + 1);
        }

        return asset($base === '' ? $path : $base.'/'.$path);
    }

    /**
     * Same as url(), but returns null when the file is not actually present.
     *
     * Useful for optional media (hero photo or clip, logo) so a missing upload
     * falls back to the caller's placeholder instead of rendering a broken
     * element. A remote URL cannot be checked on disk, so it is returned as-is.
     */
    public static function existingUrl(string $path, ?string $folder = null): ?string
    {
        // A remote file cannot be checked on disk, so a configured URL is
        // trusted as-is. The check is on the input, not on the built URL:
        // asset() itself returns an absolute http:// URL for local files.
        if (static::isRemote($path)) {
            return static::url($path, $folder);
        }

        $url = static::url($path, $folder);

        $relative = parse_url($url, PHP_URL_PATH) ?: '';

        return is_file(public_path(ltrim($relative, '/'))) ? $url : null;
    }

    /**
     * Build an asset URL for a file, typed for direct echo in Blade.
     */
    public static function tag(string $path, ?string $folder = null): HtmlString
    {
        return new HtmlString(static::url($path, $folder));
    }

    /**
     * Whether the configured value is already a usable URL rather than a path
     * that still needs the media base prepended.
     */
    protected static function isRemote(string $path): bool
    {
        return (bool) preg_match('#^(https?:)?//#i', trim($path));
    }
}
