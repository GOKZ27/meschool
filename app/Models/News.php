<?php

namespace App\Models;

use App\Support\Images;
use App\Support\Videos;
use Database\Factories\NewsFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A news article.
 *
 * Categories are not their own table: the catalogue lives in
 * config('school.news.categories') so the label, icon and tone used by the
 * chips, the filters and the cards all come from one place, exactly like the
 * programs and the staff sections do.
 */
#[Fillable([
    'slug', 'title', 'excerpt', 'body', 'category', 'image', 'gallery',
    'video', 'author', 'views', 'published_at', 'is_featured', 'is_picked',
])]
class News extends Model
{
    /** @use HasFactory<NewsFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'is_featured' => 'boolean',
            'is_picked' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /* --------------------------------------------------------------------
     | Scopes
     | -------------------------------------------------------------------- */

    /** Anything already published, which is everything a visitor may read. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published_at', '<=', now());
    }

    /** Reverse-chronological, the order the whole archive is read in. */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('published_at');
    }

    /* --------------------------------------------------------------------
     | Categories
     | -------------------------------------------------------------------- */

    /**
     * The configured entry for this article's category, or a plain fallback
     * so an unknown slug still renders a readable label instead of a hole.
     *
     * @return array{label: string, icon: string, tone: string}
     */
    public function categoryInfo(): array
    {
        $known = config('school.news.categories.'.$this->category);

        if (is_array($known)) {
            return $known + ['label' => $this->category, 'icon' => 'document', 'tone' => 'school'];
        }

        return ['label' => ucfirst($this->category), 'icon' => 'document', 'tone' => 'school'];
    }

    public function categoryLabel(): string
    {
        return $this->categoryInfo()['label'];
    }

    /**
     * The publication date in Indonesian, e.g. "2 Oktober 2026". The months
     * come from config because the framework's own translation is English and
     * the site copy is Indonesian.
     */
    public function dateLabel(): string
    {
        $months = config('school.news.months', []);
        $date = $this->published_at;

        if (! $date) {
            return '';
        }

        return $date->day.' '.($months[(int) $date->month] ?? $date->format('F')).' '.$date->year;
    }

    /* --------------------------------------------------------------------
     | Media
     | -------------------------------------------------------------------- */

    /** The lead photo, or null when it has not been uploaded yet. */
    public function imageUrl(): ?string
    {
        return $this->image ? Images::existingUrl($this->image, 'berita') : null;
    }

    /**
     * Every extra photo with the URL it resolved to (null when the file is
     * missing), so a view can decide between the photo and its placeholder
     * without losing the caption or the link back to the article.
     *
     * @return array<int, array{url: ?string, caption: string, slug: string, category: string}>
     */
    public function galleryEntries(): array
    {
        return collect($this->gallery ?? [])->map(fn ($path) => [
            'url' => Images::existingUrl((string) $path, 'berita'),
            'caption' => $this->title,
            'slug' => $this->slug,
            'category' => $this->category,
        ])->all();
    }

    /**
     * The playable clip, or null when there is none yet.
     *
     * A remote URL is returned untouched — it cannot be checked from here —
     * while a local path only resolves once the file actually exists, so an
     * article can declare a video that has not been uploaded without ever
     * rendering a player that fails to load.
     */
    public function videoUrl(): ?string
    {
        if (! $this->video) {
            return null;
        }

        return preg_match('#^(https?:)?//#i', $this->video)
            ? $this->video
            : Videos::existingUrl($this->video);
    }

    /** True when the article declares a clip, uploaded or not. */
    public function declaresVideo(): bool
    {
        return $this->video !== null && $this->video !== '';
    }

    /* --------------------------------------------------------------------
     | Body
     | -------------------------------------------------------------------- */

    /**
     * The body split into paragraphs on blank lines. Stored as plain text and
     * escaped on the way out, so a contributor can never inject markup.
     *
     * @return array<int, string>
     */
    public function paragraphs(): array
    {
        $blocks = preg_split('/\R{2,}/u', trim($this->body));

        return array_values(array_filter(array_map('trim', $blocks ?: [])));
    }
}
