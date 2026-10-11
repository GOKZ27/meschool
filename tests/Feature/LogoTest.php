<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LogoTest extends TestCase
{
    #[Test]
    public function the_configured_logo_file_is_used_and_exists(): void
    {
        $path = config('school.logo.path');

        $this->assertSame('images/logo/logo2.png', $path);
        $this->assertFileExists(public_path($path));
    }

    #[Test]
    public function the_logo_is_rendered_as_an_image_on_the_home_page(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $logos = $xp->query('//img[@src="'.asset('images/logo/logo2.png').'"]');

        $this->assertGreaterThan(0, $logos->length, 'The configured logo is not rendered.');
        $this->assertStringContainsString(config('school.logo.alt'), $html);

        // Intrinsic size is declared so the bar does not reflow while the file loads.
        foreach ($logos as $logo) {
            $this->assertSame('219', $logo->getAttribute('width'));
            $this->assertSame('274', $logo->getAttribute('height'));
            $this->assertStringContainsString('object-contain', $logo->getAttribute('class'));
        }
    }

    #[Test]
    public function the_logo_is_legible_on_the_dark_navbar(): void
    {
        // The artwork is dark navy on transparent, so the navbar copy needs a
        // light plate behind it or it vanishes into school-800.
        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $nav = $xp->query('//nav')->item(0);

        $this->assertNotNull($nav, 'The navbar is missing.');

        $logo = $xp->query('.//img[contains(@src,"logo2.png")]', $nav)->item(0);

        $this->assertNotNull($logo, 'The navbar brand should use the real logo.');
        $this->assertStringContainsString('bg-white', $logo->getAttribute('class'));

        // The white header needs no plate.
        $header = $xp->query('//header')->item(0);
        $headerLogo = $xp->query('.//img[contains(@src,"logo2.png")]', $header)->item(0);

        $this->assertNotNull($headerLogo);
        $this->assertStringNotContainsString('bg-white', $headerLogo->getAttribute('class'));
    }

    #[Test]
    public function the_logo_emits_a_single_class_attribute(): void
    {
        // Regression guard. Emitting @class(...) alongside
        // $attributes->merge(['class' => ...]) writes two class="..." attributes
        // and the browser keeps only the first, which silently discarded the
        // caller's sizing and rendered the mark at its full 219x274 intrinsic
        // size, blowing the navbar out.
        $html = $this->get(route('home'))->assertOk()->getContent();

        preg_match_all('/<img[^>]*logo2\.png[^>]*>/', $html, $matches);

        $this->assertNotEmpty($matches[0], 'The logo <img> was not found.');

        foreach ($matches[0] as $tag) {
            $this->assertSame(
                1,
                substr_count($tag, 'class='),
                "The logo must emit exactly one class attribute: {$tag}",
            );
        }
    }

    #[Test]
    public function the_logo_is_sized_by_height_so_it_cannot_outgrow_the_bars(): void
    {
        // The mark is portrait (219x274). A square `size-*` box would either
        // letterbox it or force the strip taller, so callers must pin a height
        // and let the width follow the aspect ratio instead.
        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $logos = $xp->query('//img[contains(@src,"logo2.png")]');

        $this->assertGreaterThan(0, $logos->length);

        foreach ($logos as $logo) {
            $class = $logo->getAttribute('class');

            $this->assertDoesNotMatchRegularExpression(
                '/\bsize-\d/',
                $class,
                "Logo must not use a square box: {$class}",
            );
            $this->assertMatchesRegularExpression(
                '/\bh-\d/',
                $class,
                "Logo must pin an explicit height: {$class}",
            );
            $this->assertStringContainsString('w-auto', $class);
            $this->assertStringContainsString('shrink-0', $class);
        }

        // The navbar bar is 64px tall; the logo and its padding must stay
        // under that or the bar grows around the mark.
        $navbar = $xp->query('//nav')->item(0);
        $barLogo = $xp->query('.//img[contains(@src,"logo2.png")]', $navbar)->item(0);

        $this->assertNotNull($barLogo);
        $this->assertStringContainsString('h-8', $barLogo->getAttribute('class'));
    }

    #[Test]
    public function the_logo_falls_back_to_a_placeholder_when_the_file_is_missing(): void
    {
        config()->set('school.logo.path', 'images/logo/tidak-ada.png');

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringNotContainsString('images/logo/tidak-ada.png', $html);
        $this->assertStringContainsString('role="img"', $html);
    }
}
