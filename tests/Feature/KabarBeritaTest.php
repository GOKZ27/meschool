<?php

namespace Tests\Feature;

use App\Models\News;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

/**
 * The "Kabar Berita" section is a standalone editorial component: it takes a
 * flat, newest-first pool of articles and fills four distinct layouts from it,
 * skipping any block whose slice is empty. These tests pin that contract — the
 * layout markers, that no pool story is printed twice, and the empty state.
 */
class KabarBeritaTest extends TestCase
{
    private function render(int $count = 12): string
    {
        $articles = News::factory()->count($count)->create();

        return Blade::render('<x-kabar-berita :articles="$articles" />', [
            'articles' => $articles,
        ]);
    }

    public function test_it_fills_all_four_layouts_from_the_article_pool(): void
    {
        [, $path] = $this->parseHtml($this->render());

        $this->assertSame(1, $path->query('//*[@id="kabar-berita"]')->length);
        $this->assertSame(1, $path->query('//*[@data-kabar-hero]')->length);
        $this->assertSame(1, $path->query('//*[@data-kabar-popular]')->length);
        $this->assertSame(1, $path->query('//*[@data-kabar-highlight]')->length);
        $this->assertSame(1, $path->query('//*[@data-kabar-grid]')->length);
        $this->assertSame(1, $path->query('//*[@data-kabar-list]')->length);

        $this->assertSame(3, $path->query('//*[@data-kabar-grid]/article')->length);
        $this->assertSame(4, $path->query('//*[@data-kabar-list]/li')->length);
    }

    public function test_each_pool_story_is_printed_only_once(): void
    {
        $articles = News::factory()->count(12)->create();

        $html = Blade::render('<x-kabar-berita :articles="$articles" />', [
            'articles' => $articles,
        ]);

        foreach ($articles as $article) {
            $this->assertSame(
                1,
                substr_count($html, route('berita.show', $article->slug)),
                'Article '.$article->slug.' should appear exactly once.'
            );
        }
    }

    public function test_the_news_list_carries_no_numbers_or_ordered_list(): void
    {
        $html = $this->render();

        $this->assertStringNotContainsString('<ol', $html);
    }

    public function test_it_renders_an_empty_state_without_articles(): void
    {
        $html = Blade::render('<x-kabar-berita />');

        $this->assertStringContainsString('Belum ada berita', $html);
        $this->assertStringNotContainsString('data-kabar-hero', $html);
    }

    public function test_the_closing_button_points_at_the_archive(): void
    {
        $articles = News::factory()->count(1)->create();

        $html = Blade::render('<x-kabar-berita :articles="$articles" :allUrl="$url" allLabel="Selengkapnya" />', [
            'articles' => $articles,
            'url' => 'https://example.test/berita',
        ]);

        $this->assertStringContainsString('https://example.test/berita', $html);
        $this->assertStringContainsString('Selengkapnya', $html);
    }
}
