<?php

namespace Tests\Feature;

use App\Models\News;
use Tests\TestCase;

class SeoTest extends TestCase
{
    public function test_pages_share_canonical_open_graph_and_structured_data(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString('<link rel="canonical"', $html);
        $this->assertStringContainsString('property="og:title"', $html);
        $this->assertStringContainsString('property="og:image"', $html);
        $this->assertStringContainsString('name="twitter:card"', $html);
        $this->assertStringContainsString('application/ld+json', $html);
        $this->assertStringContainsString('EducationalOrganization', $html);
        $this->assertStringContainsString('name="robots" content="index, follow', $html);
    }

    public function test_the_news_archive_serves_its_own_title_and_description(): void
    {
        $html = $this->get(route('berita'))->assertOk()->getContent();

        $this->assertStringContainsString('<title>Berita — '.config('school.name').'</title>', $html);
        $this->assertStringContainsString(config('school.news.archive.intro'), $html);
    }

    public function test_an_article_renders_newsarticle_and_breadcrumb_data(): void
    {
        $article = News::factory()->create([
            'title' => 'Uji SEO Artikel',
            'slug' => 'uji-seo-artikel',
            'category' => 'kegiatan',
            'published_at' => now()->subDay(),
        ]);

        $html = $this->get(route('berita.show', $article->slug))->assertOk()->getContent();

        $this->assertStringContainsString('NewsArticle', $html);
        $this->assertStringContainsString('BreadcrumbList', $html);
        $this->assertStringContainsString('property="og:type" content="article"', $html);
    }

    public function test_a_placeholder_page_is_noindex(): void
    {
        $html = $this->get(route('search'))->assertOk()->getContent();

        $this->assertStringContainsString('name="robots" content="noindex, follow"', $html);
    }

    public function test_the_sitemap_lists_the_static_pages_and_every_article(): void
    {
        News::factory()->create([
            'slug' => 'ada-di-sitemap',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/sitemap.xml')->assertOk();

        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $body = $response->getContent();

        $this->assertStringContainsString('<urlset', $body);
        $this->assertStringContainsString(route('home'), $body);
        $this->assertStringContainsString(route('berita.show', 'ada-di-sitemap'), $body);
    }

    public function test_robots_points_at_the_sitemap(): void
    {
        $body = $this->get('/robots.txt')->assertOk()->getContent();

        $this->assertStringContainsString('Sitemap: '.url('/sitemap.xml'), $body);
        $this->assertStringContainsString('Disallow: /pencarian', $body);
    }
}
