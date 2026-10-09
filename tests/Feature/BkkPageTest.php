<?php

namespace Tests\Feature;

use App\Support\Bkk;
use Tests\TestCase;

class BkkPageTest extends TestCase
{
    /**
     * The home page carries a BKK section after the news preview, titled by
     * the config and leading to the archive.
     */
    public function test_the_home_page_renders_the_bkk_section(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $section = $xp->query('//*[@id="bkk"]')->item(0);
        $this->assertNotNull($section, 'The BKK section is missing from the home page.');

        $this->assertStringContainsString(config('school.bkk.home.title'), $html);
        $this->assertStringContainsString(config('school.bkk.home.intro'), $html);
        $this->assertStringContainsString('href="'.route('bkk').'"', $html);
        $this->assertStringContainsString(config('school.bkk.home.all_link'), $html);

        $cards = $xp->query('.//*[@data-reveal-group]//li', $section);
        $this->assertSame((int) config('school.bkk.home_count', 4), $cards->length);
    }

    /**
     * The preview follows the news section, and the two keep their alternating
     * backgrounds: news sits on slate, BKK on white.
     */
    public function test_the_bkk_section_follows_the_news_and_keeps_background_alternation(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        $news = strpos($html, 'id="berita"');
        $bkk = strpos($html, 'id="bkk"');

        $this->assertNotFalse($news);
        $this->assertNotFalse($bkk);
        $this->assertLessThan($bkk, $news);

        [, $xp] = $this->parseHtml($html);
        $section = $xp->query('//*[@id="bkk"]')->item(0);
        $this->assertStringContainsString('bg-white', $section->getAttribute('class'));
    }

    /**
     * The preview only lists listings that still take applications, so a
     * closed job never ends up on the home page.
     */
    public function test_the_home_section_never_shows_a_closed_listing(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        $closed = Bkk::jobs()->where('status', 'closed')->first();

        $this->assertNotNull($closed);
        $this->assertStringNotContainsString(e($closed['position']), $html);
        $this->assertStringNotContainsString(e($closed['company']), $html);
    }

    /**
     * Every card on the page links into a detail page.
     */
    public function test_every_archive_card_links_into_a_detail_page(): void
    {
        $html = $this->get(route('bkk'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $cards = $xp->query('//ul[@data-reveal-group]//li//a[@href]');
        $this->assertSame((int) config('school.bkk.per_page'), $cards->length);

        foreach (iterator_to_array($cards) as $card) {
            $this->assertStringContainsString('/bkk/', $card->getAttribute('href'));
        }
    }

    /**
     * Listings come back newest first, so the most recently published job
     * opens the page ahead of what was shared just before it.
     */
    public function test_the_archive_is_newest_first(): void
    {
        $newest = Bkk::jobs()->get(0);
        $older = Bkk::jobs()->get((int) config('school.bkk.per_page') - 1);

        $html = $this->get(route('bkk'))->assertOk()->getContent();

        $this->assertLessThan(
            strpos($html, e($older['position'])),
            strpos($html, e($newest['position'])),
        );
    }

    /**
     * Nine listings against a per_page of six gives two pages, so the archive
     * actually paginates rather than dumping everything in one column.
     */
    public function test_the_archive_paginates(): void
    {
        $perPage = (int) config('school.bkk.per_page');
        $total = Bkk::jobs()->count();

        $this->assertGreaterThan($perPage, $total);

        $html = $this->get(route('bkk'))->assertOk()->getContent();
        $this->assertStringContainsString('?page=2', $html);

        [, $xp] = $this->parseHtml($html);
        $cards = $xp->query('//ul[@data-reveal-group]//li');
        $this->assertSame($perPage, $cards->length);

        $pageTwo = $this->get(route('bkk', ['page' => 2]))->assertOk()->getContent();
        [, $xp] = $this->parseHtml($pageTwo);
        $cards = $xp->query('//ul[@data-reveal-group]//li');
        $this->assertSame($total - $perPage, $cards->length);
    }

    public function test_search_narrows_the_archive(): void
    {
        $html = $this->get(route('bkk', ['q' => 'barista']))->assertOk()->getContent();

        $this->assertStringContainsString('Kedai Kopi Senja', $html);
        $this->assertStringNotContainsString('PT Nusantara Fiber', $html);

        [, $xp] = $this->parseHtml($html);
        $this->assertSame(1, $xp->query('//ul[@data-reveal-group]//li')->length);
    }

    public function test_the_field_filter_narrows_the_archive(): void
    {
        $category = Bkk::categories()->first();

        $expected = Bkk::jobs()->where('category', $category['slug'])->pluck('position');
        $others = Bkk::jobs()->where('category', '!==', $category['slug'])->pluck('position');

        $html = $this->get(route('bkk', ['bidang' => $category['slug']]))->assertOk()->getContent();

        foreach ($expected as $position) {
            $this->assertStringContainsString(e($position), $html);
        }
        if ($others->isNotEmpty()) {
            $this->assertStringNotContainsString(e($others->first()), $html);
        }
    }

    public function test_the_location_filter_narrows_the_archive(): void
    {
        $location = Bkk::locations()->first();

        $expected = Bkk::jobs()->where('location', $location['value'])->pluck('position');
        $others = Bkk::jobs()->where('location', '!==', $location['value'])->pluck('position');

        $html = $this->get(route('bkk', ['lokasi' => $location['value']]))->assertOk()->getContent();

        foreach ($expected as $position) {
            $this->assertStringContainsString(e($position), $html);
        }
        if ($others->isNotEmpty()) {
            $this->assertStringNotContainsString(e($others->first()), $html);
        }
    }

    /**
     * The status filter matches whatever the deadline-derived status says, so
     * it keeps agreeing with the cards as the dates drift.
     */
    public function test_the_status_filter_matches_the_derived_status(): void
    {
        foreach (['open', 'closing', 'closed'] as $status) {
            $expected = Bkk::jobs()->where('status', $status)->pluck('position');
            $others = Bkk::jobs()->where('status', '!==', $status)->pluck('position');

            $html = $this->get(route('bkk', ['status' => $status]))->assertOk()->getContent();

            [, $xp] = $this->parseHtml($html);
            $expectedOnPage = min($expected->count(), (int) config('school.bkk.per_page'));
            $this->assertSame($expectedOnPage, $xp->query('//ul[@data-reveal-group]//li')->length);

            foreach ($expected as $position) {
                $this->assertStringContainsString(e($position), $html);
            }
            foreach ($others as $position) {
                $this->assertStringNotContainsString(e($position), $html);
            }
        }
    }

    public function test_a_missing_job_slug_returns_404(): void
    {
        $this->get(route('bkk.show', 'lowongan-tak-ada'))->assertNotFound();
    }

    /**
     * The detail page carries the whole listing: header facts, the prose
     * sections, and an apply button pointing at the job's channel.
     */
    public function test_the_detail_page_renders_the_listing(): void
    {
        $job = Bkk::jobs()->first();

        $html = $this->get(route('bkk.show', $job['slug']))->assertOk()->getContent();

        $this->assertStringContainsString(e($job['position']), $html);
        $this->assertStringContainsString(e($job['company']), $html);
        $this->assertStringContainsString($job['location'], $html);
        $this->assertStringContainsString($job['deadline_label'], $html);

        $this->assertStringContainsString(config('school.bkk.detail.description'), $html);
        $this->assertStringContainsString(config('school.bkk.detail.requirements'), $html);
        $this->assertStringContainsString(config('school.bkk.detail.qualifications'), $html);
        $this->assertStringContainsString(config('school.bkk.detail.benefits'), $html);
        $this->assertStringContainsString(config('school.bkk.detail.how_to_apply'), $html);

        $this->assertStringContainsString('href="'.e($job['apply_url']).'"', $html);

        // The first description paragraph is rendered once.
        $this->assertStringContainsString(e($job['description'][0]), $html);
    }

    /**
     * A closed listing is still readable, but its apply button becomes a "low
     * sudah ditutup" note instead of an open channel.
     */
    public function test_a_closed_listing_does_not_offer_to_apply(): void
    {
        $job = Bkk::jobs()->where('status', 'closed')->first();

        $this->assertNotNull($job, 'The fixture data needs a closed listing for this test.');

        $html = $this->get(route('bkk.show', $job['slug']))->assertOk()->getContent();

        $this->assertStringContainsString('Lowongan ini sudah ditutup', $html);
        $this->assertStringNotContainsString('href="'.e($job['apply_url']).'"', $html);
    }

    /**
     * The detail page suggests jobs from the same field as a next step back
     * into the archive.
     */
    public function test_the_detail_page_suggests_related_listings(): void
    {
        $job = Bkk::jobs()->first();

        $html = $this->get(route('bkk.show', $job['slug']))->assertOk()->getContent();

        $this->assertStringContainsString('Lowongan Terkait', $html);

        $related = Bkk::jobs()
            ->where('category', $job['category'])
            ->where('slug', '!==', $job['slug']);

        foreach ($related as $other) {
            $this->assertStringContainsString(e($other['position']), $html);
        }
    }
}
