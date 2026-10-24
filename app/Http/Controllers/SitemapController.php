<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Support\Bkk;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

/**
 * Builds /sitemap.xml from the live URLs: the static pages, one entry per
 * programme, every published article and every job listing. Everything is
 * derived from config and the database, so the sitemap never drifts from what
 * the site actually serves.
 */
class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [];

        $static = [
            ['home', 'weekly', '1.0'],
            ['berita', 'daily', '0.9'],
            ['bkk', 'daily', '0.9'],
            ['spmb', 'monthly', '0.8'],
            ['tenaga-kependidikan', 'monthly', '0.6'],
            ['kontak', 'yearly', '0.5'],
        ];

        foreach ($static as [$name, $changefreq, $priority]) {
            $urls[] = [
                'loc' => route($name),
                'changefreq' => $changefreq,
                'priority' => $priority,
                'lastmod' => null,
            ];
        }

        foreach (array_keys(config('school.program_catalog.items', [])) as $slug) {
            if (Route::has('program.'.$slug)) {
                $urls[] = [
                    'loc' => route('program.'.$slug),
                    'changefreq' => 'monthly',
                    'priority' => '0.7',
                    'lastmod' => null,
                ];
            }
        }

        foreach (News::published()->latestFirst()->get() as $article) {
            $urls[] = [
                'loc' => route('berita.show', $article->slug),
                'changefreq' => 'monthly',
                'priority' => '0.6',
                'lastmod' => $article->updated_at?->toAtomString(),
            ];
        }

        foreach (Bkk::jobs() as $job) {
            $urls[] = [
                'loc' => route('bkk.show', $job['slug']),
                'changefreq' => 'weekly',
                'priority' => '0.6',
                'lastmod' => Carbon::parse($job['published_at'])->toAtomString(),
            ];
        }

        return response($this->render($urls), 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * @param  array<int, array{loc: string, changefreq: ?string, priority: ?string, lastmod: ?string}>  $urls
     */
    private function render(array $urls): string
    {
        $lines = [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
        ];

        foreach ($urls as $url) {
            $lines[] = '  <url>';
            $lines[] = '    <loc>'.e($url['loc']).'</loc>';

            if (! empty($url['lastmod'])) {
                $lines[] = '    <lastmod>'.e($url['lastmod']).'</lastmod>';
            }

            if (! empty($url['changefreq'])) {
                $lines[] = '    <changefreq>'.e($url['changefreq']).'</changefreq>';
            }

            if (! empty($url['priority'])) {
                $lines[] = '    <priority>'.e($url['priority']).'</priority>';
            }

            $lines[] = '  </url>';
        }

        $lines[] = '</urlset>';

        return implode("\n", $lines)."\n";
    }
}
