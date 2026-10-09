<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FooterTest extends TestCase
{
    #[Test]
    public function home_renders_a_contact_section_after_the_spmb_banner(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertGreaterThan(
            strpos($html, 'id="spmb"'),
            strpos($html, 'id="kontak"'),
            'The contact section must follow the SPMB banner.',
        );

        [, $xp] = $this->parseHtml($html);

        $section = $xp->query('//*[@id="kontak"]')->item(0);

        $this->assertNotNull($section, 'The home page is missing the contact section.');
        $this->assertStringContainsString('Hubungi Sekolah', $section->textContent);

        $hrefs = array_map(
            fn ($card) => $card->getAttribute('href'),
            iterator_to_array($xp->query('.//a[@href]', $section)),
        );

        // The four channels plus the four social links live inside the section,
        // so only check that the channel destinations are among them.
        foreach ([
            config('school.contact.whatsapp_link'),
            config('school.contact.phone_link'),
            'mailto:'.config('school.contact.email'),
            config('school.contact.maps_url'),
        ] as $expected) {
            $this->assertContains($expected, $hrefs, "The contact section must link to {$expected}.");
        }

        $this->assertSame(1, $xp->query('.//iframe[contains(@title, "Peta lokasi") and contains(@src, "output=embed")]', $section)->length);
    }

    #[Test]
    public function the_contact_section_reaches_every_social_channel(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        foreach (config('school.socials') as $social) {
            $this->assertStringContainsString($social['url'], $html);
            $this->assertStringContainsString($social['label'], $html);
        }
    }

    #[Test]
    public function the_footer_renders_on_every_page(): void
    {
        foreach ([route('home'), route('kontak'), route('bkk'), route('berita'), route('spmb')] as $url) {
            $html = $this->get($url)->assertOk()->getContent();

            $this->assertMatchesRegularExpression('/<footer/', $html, "No <footer> on {$url}.");
            $this->assertStringContainsString(config('school.name'), $html);
            $this->assertStringContainsString('&copy; '.date('Y'), $html);

            // Compare paths, not full URLs: inside a test the active request
            // decides the host/port that `route()` renders against, so the
            // footer hrefs are not guaranteed to match `route()` called here.
            foreach (['berita', 'bkk', 'spmb', 'kontak', 'tenaga-kependidikan'] as $routeName) {
                $this->assertStringContainsString(
                    route($routeName, [], false),
                    $html,
                    "Footer missing route {$routeName} on {$url}.",
                );
            }
        }
    }

    #[Test]
    public function the_footer_lives_outside_main(): void
    {
        // A <footer> may not be a descendant of <main>. The site-wide footer is
        // emitted by the layout, so it must close each page's single <main> as
        // a sibling instead of nesting inside it.
        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertLessThan(strpos($html, '<footer'), strpos($html, '</main>'));

        [, $xp] = $this->parseHtml($html);

        $this->assertSame(1, $xp->query('//footer')->length);
        $this->assertSame(0, $xp->query('//main//footer')->length);
        $this->assertSame(1, $xp->query('//main')->length);
    }
}
