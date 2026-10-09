<?php

namespace Tests\Feature;

use Tests\TestCase;

class SpmbPageTest extends TestCase
{
    /**
     * The home page closes on a full-width SPMB banner carrying the configured
     * copy and both calls-to-action into the SPMB page.
     */
    public function test_the_home_page_renders_the_spmb_banner(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $section = $xp->query('//*[@id="spmb"]')->item(0);
        $this->assertNotNull($section, 'The SPMB banner is missing from the home page.');

        $this->assertStringContainsString(config('school.spmb.eyebrow'), $html);
        $this->assertStringContainsString(config('school.spmb.title'), $html);
        $this->assertStringContainsString(config('school.spmb.subtitle'), $html);
        $this->assertStringContainsString(config('school.spmb.description'), $html);

        $links = $xp->query('.//a[@href="'.route('spmb').'"]', $section);
        $this->assertSame(2, $links->length);
        $this->assertStringContainsString(config('school.spmb.button_label'), (string) $links->item(0)->textContent);
        $this->assertStringContainsString(config('school.spmb.link_label'), (string) $links->item(1)->textContent);

        $this->assertSame(1, $xp->query('.//*[@data-reveal-group]', $section)->length);
    }

    /**
     * The banner sits below the BKK grid and reads as its own block: a photo
     * band with its own min-height, not another white/slate section.
     */
    public function test_the_banner_follows_the_bkk_and_stays_a_distinct_block(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        $bkk = strpos($html, 'id="bkk"');
        $spmb = strpos($html, 'id="spmb"');

        $this->assertNotFalse($bkk);
        $this->assertNotFalse($spmb);
        $this->assertLessThan($spmb, $bkk);

        [, $xp] = $this->parseHtml($html);
        $section = $xp->query('//*[@id="spmb"]')->item(0);

        $this->assertStringContainsString('min-h-[500px]', $section->getAttribute('class'));
        $this->assertStringNotContainsString('bg-white', $section->getAttribute('class'));
    }

    /**
     * The band paints the configured school photo under a dark overlay so the
     * white copy keeps its contrast.
     */
    public function test_the_banner_uses_the_configured_school_photo(): void
    {
        $photo = (string) config('school.spmb.bg_photo');

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString($photo, $html);
        $this->assertStringContainsString('data-spmb-bg', $html);
        $this->assertStringContainsString('data-spmb-overlay', $html);
    }

    /**
     * A missing photo must not leave a broken layer: fall back to the brick
     * gradient, exactly like a hero slide that has not been uploaded yet.
     */
    public function test_the_banner_falls_back_when_the_photo_is_missing(): void
    {
        config()->set('school.spmb.bg_photo', 'tidak-ada.jpg');

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringNotContainsString('tidak-ada.jpg', $html);
        $this->assertStringNotContainsString('data-spmb-bg', $html);
        $this->assertStringContainsString('from-school-700', $html);
    }

    /**
     * The /spmb landing page is the destination of both banner calls-to-action:
     * the hero reuses the banner's photo + overlay, and the body lays out the
     * registration flow straight from config.
     */
    public function test_the_landing_page_renders_the_registration_flow(): void
    {
        $html = $this->get(route('spmb'))->assertOk()->getContent();
        $page = config('school.spmb_page');

        $this->assertStringContainsString($page['title'], $html);
        $this->assertStringContainsString($page['subtitle'], $html);
        $this->assertStringContainsString($page['intro'], $html);

        foreach ($page['steps'] as $step) {
            $this->assertStringContainsString($step['title'], $html);
            $this->assertStringContainsString($step['description'], $html);
        }

        foreach ($page['requirements'] as $requirement) {
            $this->assertStringContainsString($requirement, $html);
        }

        [, $xp] = $this->parseHtml($html);

        $this->assertSame(count($page['steps']), $xp->query('//*[@data-spmb-steps]/li')->length);
        $this->assertSame(count($page['requirements']), $xp->query('//*[@data-spmb-requirements]/li')->length);

        $cta = $xp->query('//*[@data-spmb-cta]');
        $this->assertSame(1, $cta->length);
        $this->assertSame(1, $xp->query('//*[@data-spmb-cta]//a[@href="'.config('school.contact.whatsapp_link').'"]')->length);
        $this->assertSame(1, $xp->query('//*[@data-spmb-cta]//a[@href="'.route('kontak').'"]')->length);

        $this->assertStringContainsString(config('school.spmb.bg_photo'), $html);
        $this->assertStringContainsString('data-spmb-bg', $html);
        $this->assertStringContainsString(config('school.spmb.overlay'), $html);
    }

    /**
     * The landing page hero falls back to the brick gradient when the shared
     * SPMB photo is missing, exactly like the home banner does.
     */
    public function test_the_landing_page_falls_back_when_the_photo_is_missing(): void
    {
        config()->set('school.spmb.bg_photo', 'tidak-ada.jpg');

        $html = $this->get(route('spmb'))->assertOk()->getContent();

        $this->assertStringNotContainsString('data-spmb-bg', $html);
        $this->assertStringContainsString('from-school-700', $html);
    }
}
