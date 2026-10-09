<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Decoration and queries for the BKK job listings.
 *
 * The listings live in config/school.php — there is no jobs table yet, so the
 * working copy is the array. Every decorated job carries its raw fields plus
 * the derived ones the views share: the status derived from the deadline, the
 * Indonesian date labels, the category label and the logo/monogram fallback.
 */
class Bkk
{
    /**
     * Every job, decorated and newest first.
     *
     * @return Collection<int, array>
     */
    public static function jobs(): Collection
    {
        return collect(config('school.bkk.jobs', []))
            ->map(fn (array $job) => static::decorate($job))
            ->sortByDesc(fn (array $job) => $job['published_at'])
            ->values();
    }

    /**
     * Jobs that are still taking applications (open or closing soon).
     *
     * @return Collection<int, array>
     */
    public static function active(): Collection
    {
        return static::jobs()->reject(fn (array $job) => $job['status'] === 'closed');
    }

    /**
     * The slice of active jobs the home page preview shows.
     *
     * @return Collection<int, array>
     */
    public static function forHome(): Collection
    {
        return static::active()->take((int) config('school.bkk.home_count', 4));
    }

    /**
     * One decorated job by slug, or null when nothing matches.
     */
    public static function find(string $slug): ?array
    {
        return static::jobs()->firstWhere('slug', $slug);
    }

    /**
     * The category catalogue with per-category job counts.
     *
     * @return Collection<int, array{slug: string, label: string, count: int}>
     */
    public static function categories(): Collection
    {
        $counts = static::jobs()->countBy('category');

        return collect(config('school.bkk.categories', []))
            ->map(fn (array $info, string $slug) => $info + [
                'slug' => $slug,
                'count' => (int) $counts->get($slug, 0),
            ])
            ->values();
    }

    /**
     * Every distinct office location with its job count, busiest first.
     *
     * @return Collection<int, array{value: string, label: string, count: int}>
     */
    public static function locations(): Collection
    {
        return static::jobs()
            ->countBy('location')
            ->sortDesc()
            ->map(fn (int $count, string $location) => [
                'value' => $location,
                'label' => $location,
                'count' => $count,
            ])
            ->values();
    }

    /**
     * The three statuses with their job counts, in the order the filter list
     * shows them (closed last).
     *
     * @return Collection<int, array{value: string, label: string, count: int}>
     */
    public static function statuses(): Collection
    {
        $counts = static::jobs()->countBy('status');

        return collect(config('school.bkk.status', []))
            ->reject(fn (string $label, string $status) => $status === 'closed')
            ->map(fn (string $label, string $status) => [
                'value' => $status,
                'label' => $label,
                'count' => (int) $counts->get($status, 0),
            ])
            ->values();
    }

    /**
     * "30 November 2026" — the Indonesian month names live with the news
     * feature, because the framework ships English only.
     */
    public static function dateLabel(string $date): string
    {
        $date = Carbon::parse($date);
        $months = config('school.news.months', []);

        return $date->format('j').' '.($months[$date->month] ?? $date->format('F')).' '.$date->format('Y');
    }

    /**
     * Add the derived fields to one raw config entry.
     */
    private static function decorate(array $job): array
    {
        $deadline = Carbon::parse($job['deadline']);
        $today = Carbon::today();

        $daysLeft = $today->diffInDays($deadline, false);
        $status = match (true) {
            $deadline->isBefore($today) => 'closed',
            $daysLeft <= (int) config('school.bkk.closing_days', 7) => 'closing',
            default => 'open',
        };

        $category = $job['category'] ?? '';
        $categoryLabel = (string) config("school.bkk.categories.{$category}.label", ucfirst($category));

        return $job + [
            'status' => $status,
            'status_label' => (string) config("school.bkk.status.{$status}", ucfirst($status)),
            'deadline_label' => static::dateLabel($job['deadline']),
            'published_label' => static::dateLabel($job['published_at']),
            'category_label' => $categoryLabel !== '' ? $categoryLabel : null,
            'logo_url' => $job['logo'] !== null
                ? Images::existingUrl((string) $job['logo'], 'bkk')
                : null,
            'logo_mark' => static::initials((string) $job['company']),
            'is_active' => $status !== 'closed',
            'salary' => $job['salary'] ?? null,
            'apply_url' => (string) ($job['apply'] ?? ('mailto:'.config('school.contact.email').'?subject='.rawurlencode('Lamaran '.$job['position']))),
            'sort_key' => $job['published_at'],
        ];
    }

    /**
     * Two capital initials from the company name, used in the logo tile while
     * an actual logo file is missing.
     */
    private static function initials(string $name): string
    {
        $words = preg_split('/\s+/', trim($name)) ?: [];

        return mb_strtoupper(
            implode('', array_slice(array_map(
                fn (string $word) => mb_substr($word, 0, 1),
                $words,
            ), 0, 2)),
        );
    }
}
