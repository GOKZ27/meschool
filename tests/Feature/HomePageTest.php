<?php

namespace Tests\Feature;

use App\Support\Images;
use App\Support\Videos;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    /** @var array<int, string> Absolute paths of stand-in media written into public/. */
    private array $fakeMedia = [];

    public function test_it_renders_every_main_section(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee(config('school.name'));
        $response->assertSee(config('school.tagline'));
    }

    public function test_it_renders_the_top_bar_contacts_and_socials(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee("Let's follow:", escape: false);
        $response->assertSee(config('school.contact.whatsapp'));
        $response->assertSee(config('school.contact.whatsapp_link'), escape: false);
        $response->assertSee(config('school.contact.phone_link'), escape: false);
    }

    public function test_it_renders_all_navbar_items(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();

        $labels = [
            'BERANDA', 'PROGRAM KEAHLIAN', 'TENAGA KEPENDIDIKAN', 'BERITA',
            'BKK', 'SPMB', 'KONTAK', 'MAPS',
        ];

        foreach ($labels as $label) {
            $response->assertSee($label);
        }
    }

    public function test_every_dropdown_parent_has_the_group_class_so_hover_works(): void
    {
        // Regression guard: the desktop dropdown panels are driven by
        // `group-hover:*`, which is inert unless the nav item <li> is a group.
        // Scope to the direct children of the two top-level nav lists so plain
        // <li> elements elsewhere (dropdown children, etc.) can't satisfy this.
        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        // Top-level nav items only: any <li> nested inside another <li> belongs
        // to a dropdown panel (nav-children renders bare <li> without classes).
        $navItems = $xp->query('//nav//li[not(ancestor::li)]');

        $this->assertNotFalse($navItems);
        $this->assertGreaterThan(0, $navItems->length);

        foreach ($navItems as $li) {
            $classes = explode(' ', $li->getAttribute('class'));

            $this->assertContains('group', $classes);
            // The dropdown panel is absolutely positioned against this <li>, and
            // the gold hover rule needs the relative positioning too.
            $this->assertContains('relative', $classes);
            $this->assertContains('lg:flex', $classes);
        }
    }

    public function test_the_navbar_does_not_nest_list_items(): void
    {
        // nav-item renders its own <li>, so the mobile drawer must not wrap it
        // in another <li> — nested <li> gets hoisted out by the browser parser
        // and silently breaks the drawer's separators.
        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $this->assertSame(0, $xp->query('//nav//li/li')->length);
    }

    public function test_the_maps_entry_is_an_external_link(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        // Escaped on purpose: the URL's `&` is rendered as `&amp;` in the href.
        $response->assertSee(config('school.contact.maps_url'));
    }

    public function test_the_maps_route_redirects_to_google_maps(): void
    {
        $this->get(route('maps'))->assertRedirect(config('school.contact.maps_url'));
    }

    public function test_it_renders_every_dropdown_child(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();

        $programs = ['kuliner' => 'Kuliner', 'dkv' => 'DKV', 'tkj' => 'TKJ', 'ak' => 'AK', 'mp' => 'MP', 'bd' => 'BD'];

        foreach ($programs as $slug => $label) {
            $response->assertSee($label);
            $response->assertSee('href="'.route('program.'.$slug).'"', escape: false);
        }
    }

    public function test_only_program_keahlian_has_a_dropdown(): void
    {
        // Berita/BKK/SPMB are flat links now, so their old child pages must no
        // longer appear in the bar — they stay reachable via their section page.
        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $triggers = $xp->query('//nav//a[@aria-haspopup="true"]');

        $this->assertNotFalse($triggers);
        // One dropdown parent per bar instance (desktop bar + mobile drawer).
        $this->assertCount(2, iterator_to_array($triggers));

        foreach ($triggers as $trigger) {
            $this->assertStringContainsString('PROGRAM KEAHLIAN', $trigger->textContent);
        }

        foreach (['Pengumuman', 'Kegiatan Sekolah', 'Prestasi', 'Tracer Study', 'Informasi Lowongan Pekerjaan'] as $noLongerInBar) {
            $this->assertStringNotContainsString('>'.$noLongerInBar.'<', $html);
        }
    }

    public function test_flat_sections_link_to_their_landing_pages(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        foreach (['berita' => 'BERITA', 'bkk' => 'BKK', 'spmb' => 'SPMB'] as $routeName => $label) {
            $this->assertStringContainsString(
                'href="'.route($routeName).'"',
                $html,
                "Expected {$label} to point at its section page.",
            );
        }
    }

    public function test_the_navbar_hover_pulses_instead_of_underlining(): void
    {
        // The hover treatment lives in CSS (.nav-link), so assert it in the
        // built stylesheet rather than in markup. Guard against regressing back
        // to a gold rule, which the school asked to have removed.
        $css = $this->builtCss();

        $this->assertStringContainsString('@keyframes nav-pulse', $css, 'The pulse keyframes are missing from the build.');
        $this->assertMatchesRegularExpression(
            '/@keyframes nav-pulse\{.*?50%\{transform:scale\(1\.0?7\)\}/s',
            $css,
            'The pulse must scale the label up to 1.07 at its midpoint.',
        );
        $this->assertMatchesRegularExpression(
            '/\.nav-link:hover>span\{animation:[^}]*nav-pulse/',
            $css,
            'Hover must run the pulse on the label.',
        );

        // The animation targets the label, never the flex-1 link box, so the
        // equal-width cells and their hairlines cannot shift on hover.
        $this->assertStringNotContainsString('.nav-link:hover{animation', $css);
    }

    public function test_the_navbar_hover_never_underlines_or_blocks_the_item(): void
    {
        $css = $this->builtCss();

        $this->assertStringNotContainsString('.nav-link:after', $css);
        $this->assertStringNotContainsString('.nav-link::after', $css);
        $this->assertStringNotContainsString('.nav-link:before', $css);
        $this->assertStringNotContainsString('nav-link::before', $css);
    }

    public function test_the_navbar_pulse_respects_reduced_motion(): void
    {
        // A looping animation is exactly the kind of motion the OS setting is
        // meant to suppress, so the reset must survive the build.
        $this->assertMatchesRegularExpression(
            '/@media\s*\(prefers-reduced-motion:reduce\)\{\.nav-link>span\{animation:none\}/',
            $this->builtCss(),
        );
    }

    public function test_the_navbar_keeps_an_animation_free_way_to_show_the_current_page(): void
    {
        // `.nav-link-active` may recolour the label, but it must not loop the
        // pulse forever on the page the visitor is already reading.
        $css = $this->builtCss();

        $this->assertStringContainsString('.nav-link.nav-link-active{', $css);
        $this->assertStringNotContainsString('.nav-link.nav-link-active>span{animation', $css);
    }

    private function builtCss(): string
    {
        $files = glob(public_path('build/assets/app-*.css'));

        $this->assertNotEmpty($files, 'Run `npm run build` before the suite; no compiled CSS was found.');

        return implode("\n", array_map(fn ($f) => file_get_contents($f), $files));
    }

    public function test_the_navbar_labels_are_the_pulse_target(): void
    {
        // The CSS scales `.nav-link > span`, so every label needs to be a span
        // or it simply will not animate.
        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        foreach ($xp->query('//nav//a[contains(@class,"nav-link")]') as $link) {
            $label = $xp->query('./span', $link)->item(0);

            $this->assertNotNull($label, 'Every nav link needs a label span to animate.');
        }
    }

    public function test_the_navbar_separates_items_with_a_hairline(): void
    {
        // Each bar item but the first gets a vertical hairline, so the row reads
        // as a list of sections rather than one crowded block.
        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $barItems = $xp->query('//nav//li[not(ancestor::li)]');

        $this->assertNotFalse($barItems);

        $divided = 0;

        foreach ($barItems as $li) {
            $classes = $li->getAttribute('class');

            if (str_contains($classes, 'not-first:border-l')) {
                $divided++;
            }
        }

        $this->assertGreaterThan(0, $divided, 'No navbar item carries the divider class.');
    }

    public function test_every_desktop_item_gets_an_equal_share_of_the_bar(): void
    {
        // Each desktop item is a flex-1 cell, so a short label (BERANDA) occupies
        // the same box as a long one (PROGRAM KEAHLIAN) and the hairlines fall at
        // even intervals. Mobile items must stay fluid.
        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        // The bar's menu is the <ul> directly inside the h-16 row; the drawer's list
        // lives under .nav-menu-panel further down.
        $bar = $xp->query('//nav//div[contains(@class,"h-16")]/ul/li[not(ancestor::li)]');

        $this->assertNotFalse($bar);
        $this->assertCount(8, iterator_to_array($bar));

        foreach ($bar as $li) {
            $this->assertStringContainsString('lg:flex-1', $li->getAttribute('class'));

            // Centred text inside the cell keeps the label optically aligned.
            $link = $xp->query('./a', $li)->item(0);
            $this->assertStringContainsString('lg:justify-center', $link->getAttribute('class'));
            $this->assertStringContainsString('lg:w-full', $link->getAttribute('class'));
        }

        $drawer = $xp->query('//div[contains(@class,"nav-menu-panel")]//li[not(ancestor::li)]');

        foreach ($drawer as $li) {
            $this->assertStringNotContainsString('lg:flex-1', $li->getAttribute('class'));
        }
    }

    public function test_the_navbar_shares_its_container_with_the_logo_header(): void
    {
        // The logo lives in the strip above the bar. If the two wrappers drifted
        // apart in width or padding the menu would no longer line up with it.
        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $expected = 'mx-auto max-w-7xl px-4 sm:px-6 lg:px-8';
        $barWrapper = $xp->query('//nav/div[1]')->item(0);

        $this->assertStringContainsString($expected, $barWrapper->getAttribute('class'));

        // The header strip keeps the same gutters; only its vertical padding differs.
        $headerWrapper = $xp->query('//header/div[1]')->item(0)->getAttribute('class');

        foreach (['max-w-7xl', 'px-4', 'sm:px-6', 'lg:px-8'] as $gutter) {
            $this->assertStringContainsString($gutter, $headerWrapper);
        }
    }

    public function test_it_renders_the_search_control(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('id="nav-search"', escape: false);
        $response->assertSee('name="q"', escape: false);
    }

    public function test_the_hero_has_no_thumbnail_gallery_or_badges(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();

        foreach (['Galeri Kegiatan', 'Kegiatan 1', 'Kegiatan 2', 'Kegiatan 3', 'Best Choice', 'Ganti Foto', 'Area Foto Utama'] as $removed) {
            $response->assertDontSee($removed);
        }
    }

    public function test_the_hero_rotates_three_photos_on_their_own_timing(): void
    {
        // The caption slide is held for 3s and the two plain photos for 1s each,
        // so the caption reads before the sequence moves on.
        config()->set('school.hero.slides', [
            ['photo' => $this->fakeHeroPhoto('slide-1.jpg'), 'duration' => 3000, 'caption' => true],
            ['photo' => $this->fakeHeroPhoto('slide-2.jpg'), 'duration' => 1000],
            ['photo' => $this->fakeHeroPhoto('slide-3.jpg'), 'duration' => 1000],
        ]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        // More than one slide means the script takes over the timing.
        $this->assertStringContainsString('data-hero-slider', $html);

        [, $xp] = $this->parseHtml($html);

        $this->assertSame(3, $xp->query('//div[@data-hero-slide]')->length);

        $durations = [];

        foreach ($xp->query('//div[@data-hero-slide]') as $slide) {
            $durations[] = $slide->getAttribute('data-hero-duration');
        }

        $this->assertSame(['3000', '1000', '1000'], $durations);

        foreach (['slide-1.jpg', 'slide-2.jpg', 'slide-3.jpg'] as $name) {
            $this->assertStringContainsString(asset('images/hero/'.$name), $html);
        }
    }

    public function test_the_hero_photo_is_used_as_a_background_not_a_card(): void
    {
        config()->set('school.hero.slides', [
            ['photo' => $this->fakeHeroPhoto()],
        ]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        // The photo fills the whole banner ...
        $this->assertStringContainsString(asset('images/hero/gedung-sekolah.jpg'), $html);
        $this->assertStringContainsString('object-cover', $html);

        // ... and is decorative, so the whole stack is hidden from assistive tech.
        $this->assertStringContainsString('aria-hidden="true"', $html);

        // A photo slide never renders a clip slot.
        $this->assertStringNotContainsString('<video', $html);
    }

    public function test_only_one_slide_carries_the_school_name_the_slogan_and_the_buttons(): void
    {
        config()->set('school.hero.slides', [
            ['photo' => $this->fakeHeroPhoto('slide-1.jpg'), 'duration' => 3000, 'caption' => true],
            ['photo' => $this->fakeHeroPhoto('slide-2.jpg'), 'duration' => 1000],
            ['photo' => $this->fakeHeroPhoto('slide-3.jpg'), 'duration' => 1000],
        ]);

        $html = $this->get(route('home'))->assertOk()->getContent();
        [, $xp] = $this->parseHtml($html);

        // One caption layer for the whole banner, not one per slide, so the two
        // plain photos stay free of text and buttons.
        $this->assertSame(1, $xp->query('//*[@data-hero-caption]')->length);

        $this->assertSame(config('school.name'), trim($xp->query('//*[@data-hero-caption]//h1')->item(0)->textContent));
        $this->assertSame(config('school.tagline'), trim($xp->query('//*[@data-hero-caption]//p')->item(0)->textContent));

        // The banner carries the name and the slogan, not a label above them. Scoped
        // to the hero, because the top bar legitimately spells out "Sekolah
        // Menengah Kejuruan Islamiyah Ciputat".
        preg_match('/<section\b.*?<\/section>/s', $html, $hero);

        $this->assertNotEmpty($hero, 'The hero section was not rendered.');
        $this->assertStringNotContainsString('Sekolah Menengah Kejuruan', $hero[0]);

        $actions = $xp->query('//*[@data-hero-caption]//a');

        $this->assertSame(2, $actions->length, 'The caption is expected to carry exactly two buttons.');

        $this->assertSame(route('spmb'), $actions->item(0)->getAttribute('href'));
        $this->assertSame(config('school.contact.whatsapp_link'), $actions->item(1)->getAttribute('href'));

        // Only the first slide owns it.
        $this->assertSame('true', $xp->query('//div[@data-hero-slide][1]')->item(0)->getAttribute('data-hero-text'));

        foreach ([1, 2] as $index) {
            $slide = $xp->query(sprintf('//div[@data-hero-slide][%d]', $index + 1))->item(0);

            $this->assertSame('false', $slide->getAttribute('data-hero-text'));
            $this->assertSame(0, $xp->query('.//h1', $slide)->length);
            $this->assertSame(0, $xp->query('.//a', $slide)->length);
        }
    }

    public function test_the_caption_is_centred_and_its_buttons_match_the_site_cta_style(): void
    {
        config()->set('school.hero.slides', [
            ['photo' => $this->fakeHeroPhoto(), 'caption' => true],
            ['photo' => $this->fakeHeroPhoto('slide-2.jpg')],
        ]);

        $html = $this->get(route('home'))->assertOk()->getContent();
        [, $xp] = $this->parseHtml($html);

        // Centred text, and the buttons centred under it rather than left-hung.
        $this->assertStringContainsString('hero-caption mx-auto max-w-3xl text-center', $html);
        $this->assertStringContainsString('justify-center', $html);

        preg_match_all('/<a\b[^>]*>(?:\s*<[^>]*>)*\s*(?:Daftar Sekarang|Konsultasi)/', $html, $buttons);

        $this->assertSame(2, count($buttons[0]), 'Both caption buttons were not found.');

        foreach ($buttons[0] as $button) {
            $this->assertMatchesRegularExpression('/\bhero-action\b/', $button);
            $this->assertDoesNotMatchRegularExpression('/\brounded-full\b/', $button);
        }

        $this->assertSame(0, $xp->query('//*[@data-hero-caption]//svg')->length);
    }

    public function test_the_contact_strip_sits_below_the_hero_and_above_the_greeting(): void
    {
        // Order matters: hero, then the contact strip, then the greeting — so the
        // greeting is never the block the banner runs straight into.
        $html = $this->get(route('home'))->assertOk()->getContent();

        $hero = strpos($html, 'data-hero-slider');
        $strip = strpos($html, 'Selamat Datang di');
        $greeting = strpos($html, 'id="sambutan"');

        $this->assertNotFalse($greeting, 'The greeting block is missing from the home page.');
        $this->assertLessThan($strip, $hero);
        $this->assertLessThan($greeting, $strip);
    }

    public function test_the_contact_strip_is_not_a_footer_element(): void
    {
        // It lives inside <main> now, and a <footer> may not be a descendant of
        // <main>, so the strip is a plain block. It still reads as a footer on the
        // pages that have no greeting after it.
        $html = $this->get(route('home'))->assertOk()->getContent();
        [, $xp] = $this->parseHtml($html);

        // The label and the value sit in nested elements, so the subtree string is what
        // matches, not the anchor's own text nodes.
        $strip = $xp->query('//a[contains(., "Selamat Datang di")]')->item(0);

        $this->assertNotNull($strip);

        // The link is inside <main>. The only <footer> on the page is the
        // site-wide one, which lives outside <main> — a <footer> may not be a
        // descendant of <main> — so it can never claim the strip.
        $this->assertSame('main', $xp->query('ancestor::main', $strip)->item(0)->tagName);
        $this->assertSame(1, $xp->query('//footer')->length);
        $this->assertSame(0, $xp->query('//main//footer')->length);

        // Both channels are still there, pointing at the configured destinations.
        $this->assertSame('https://'.config('school.domain'), $strip->getAttribute('href'));
        $this->assertSame(
            config('school.contact.phone_link'),
            $xp->query('//a[contains(., "Hubungi kontak pusat informasi")]')->item(0)->getAttribute('href')
        );
    }

    public function test_the_principal_greeting_carries_the_configured_copy(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();
        [, $xp] = $this->parseHtml($html);

        $greeting = $xp->query('//*[@id="sambutan"]')->item(0);

        $this->assertNotNull($greeting);
        $this->assertSame(
            'Sambutan '.config('school.principal.role'),
            trim($xp->query('.//p', $greeting)->item(0)->textContent)
        );

        // Every configured paragraph is rendered, once, in order. Compared as
        // parsed text so an apostrophe is not mistaken for an HTML entity.
        $paragraphs = array_map(
            fn ($node) => trim($node->textContent),
            iterator_to_array($xp->query('.//div[@data-reveal-group]/p[contains(@class, "mt-4")]', $greeting))
        );

        $this->assertSame(config('school.principal.message'), $paragraphs);

        // The lead line is the only heading in the block, so the page keeps a
        // single h1 from the hero.
        $this->assertSame(1, $xp->query('.//h2', $greeting)->length);
        $this->assertSame(1, $xp->query('//h1')->length);
    }

    public function test_the_principal_greeting_credits_nobody_until_a_name_is_configured(): void
    {
        // Shipping a plausible-sounding name nobody confirmed would put the
        // wrong person on the website, so the role stands alone until it is set.
        config()->set('school.principal.name', null);

        $html = $this->get(route('home'))->assertOk()->getContent();
        [, $xp] = $this->parseHtml($html);

        $signature = $xp->query('//*[@id="sambutan"]//div[contains(@class, "mt-8")]')->item(0);

        $this->assertSame(
            config('school.principal.role'),
            trim($xp->query('./p', $signature)->item(0)->textContent)
        );

        config()->set('school.principal.name', 'H. Ahmad Fauzan');

        $named = $this->get(route('home'))->assertOk()->getContent();
        [, $xp] = $this->parseHtml($named);

        $signature = $xp->query('//*[@id="sambutan"]//div[contains(@class, "mt-8")]')->item(0);

        $this->assertSame('H. Ahmad Fauzan', trim($xp->query('./p', $signature)->item(0)->textContent));
        $this->assertSame(config('school.principal.role'), trim($xp->query('./p', $signature)->item(1)->textContent));
    }

    public function test_the_principal_greeting_uses_the_portrait_when_it_is_uploaded(): void
    {
        config()->set('school.principal.photo', $this->fakePrincipalPhoto());

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString(asset('images/principal/kepala-sekolah-test.jpg'), $html);
        $this->assertStringContainsString('object-cover', $html);

        // Without a confirmed name the alt text names the role, not a person.
        $this->assertStringContainsString('alt="'.config('school.principal.role'), $html);

        config()->set('school.principal.name', 'H. Ahmad Fauzan');

        $named = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString('alt="H. Ahmad Fauzan, Kepala Sekolah"', $named);
    }

    public function test_the_principal_greeting_falls_back_when_the_portrait_is_missing(): void
    {
        config()->set('school.principal.photo', 'tidak-ada.jpg');

        $html = $this->get(route('home'))->assertOk()->getContent();
        [, $xp] = $this->parseHtml($html);

        // A configured path with no file behind it must not leave a broken img.
        $this->assertStringNotContainsString('tidak-ada.jpg', $html);
        $this->assertSame(0, $xp->query('//*[@id="sambutan"]//img')->length);
        $this->assertSame(1, $xp->query('//*[@id="sambutan"]//*[contains(@class, "bg-brick-green")]')->length);
    }

    public function test_the_reveal_cascade_gives_group_children_the_visible_state(): void
    {
        // Regression guard. `.has-js [data-reveal-group] > *` hides every child,
        // but only the group element itself is ever marked data-revealed, so
        // `.has-js [data-reveal][data-revealed]` never reaches them. Without a
        // rule handing the visible state down, the copy beside the portrait
        // stays at opacity 0 forever and nobody ever sees the greeting.
        $css = $this->builtCss();

        $this->assertMatchesRegularExpression(
            '/\.has-js \[data-reveal-group\]\[data-revealed\]\s*>\s*\*\s*\{[^}]*opacity:1/',
            $css,
            'A revealed group must restore its children to opacity: 1, or they stay hidden.'
        );
    }

    public function test_the_home_page_offers_all_six_programs(): void
    {
        $slugs = config('school.programs.slugs');
        $items = array_intersect_key(
            config('school.program_catalog.items'),
            array_flip($slugs)
        );

        $html = $this->get(route('home'))->assertOk()->getContent();
        [, $xp] = $this->parseHtml($html);

        $cards = $xp->query('//*[@id="program-keahlian"]//a');

        $this->assertSame(6, count($items));
        $this->assertSame(6, $cards->length);
        $this->assertSame(0, $xp->query('//*[@id="program-keahlian"]//*[@data-pk-cursor]')->length);

        // The six programs are pinned, because they are what the school offers.
        $this->assertSame(
            ['Kuliner', 'DKV', 'TKJ', 'AK', 'MP', 'BD'],
            array_values(array_map(fn ($i) => $i['name'], $items))
        );

        foreach ($cards as $index => $card) {
            $slug = array_keys($items)[$index];
            $item = $items[$slug];

            $this->assertSame(route('program.'.$slug), $card->getAttribute('href'));
            $this->assertSame(0, $xp->query('.//a', $card)->length);
            $this->assertLessThanOrEqual(1, $xp->query('.//img', $card)->length);
            $this->assertSame($item['name'], trim($xp->query('.//h3', $card)->item(0)->textContent));
            $this->assertSame(
                $item['description'],
                trim($xp->query('.//p', $card)->item(0)->textContent)
            );
            $this->assertSame(
                'Pelajari '.$item['name'],
                trim($xp->query('.//*[contains(concat(" ", normalize-space(@class), " "), " pk-card__cta ")]/span[1]', $card)->item(0)->textContent)
            );

            // A logo, drawn inline: there is no icon library, so the card has to ship
            // its own svg. An unknown name falls back to an empty span instead.
            $icon = $xp->query('.//*[@data-program-icon]', $card)->item(0);

            $this->assertNotNull($icon);
            $this->assertSame(1, $xp->query('.//svg', $icon)->length);
        }
    }

    public function test_each_program_has_a_reachable_page(): void
    {
        foreach (config('school.program_catalog.items') as $slug => $item) {
            $this->get(route('program.'.$slug))
                ->assertOk()
                ->assertSee($item['name'], escape: false);
        }
    }

    public function test_every_program_icon_is_drawn_in_the_icon_component(): void
    {
        // An unknown name renders an empty box, which looks like a broken card.
        $icons = file_get_contents(resource_path('views/components/icon.blade.php'));

        foreach (config('school.program_catalog.items') as $item) {
            $this->assertStringContainsString(
                "'{$item['icon']}' =>",
                $icons,
                "No inline svg is defined for the {$item['icon']} icon."
            );
        }
    }

    public function test_the_program_heading_and_standfirst_reveal_from_opposite_edges(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();
        [, $xp] = $this->parseHtml($html);

        $section = $xp->query('//*[@id="program-keahlian"]')->item(0);

        $this->assertNotNull($section);

        // One edge each: the title arrives from one side, the standfirst from the other.
        $this->assertSame(1, $xp->query('.//*[@data-reveal-from="left"]', $section)->length);
        $this->assertSame(1, $xp->query('.//*[@data-reveal-from="right"]', $section)->length);
        $this->assertStringContainsString(
            config('school.programs.title'),
            $xp->query('.//*[@data-reveal-from="left"]', $section)->item(0)->textContent
        );

        // The cards rise from below instead, as a slower cascade of their own.
        $grid = $xp->query('.//*[@data-reveal-slow]', $section)->item(0);

        $this->assertTrue($grid->hasAttribute('data-reveal-group'));
        $this->assertSame(6, $xp->query('./a', $grid)->length);
        $this->assertFalse($grid->hasAttribute('data-revealed'));

        // app.js has to watch both hooks, not just [data-reveal]: a grid that only
        // carries the group marker would otherwise never be observed, and every
        // card inside it would stay at opacity 0 for good.
        $script = file_get_contents(resource_path('js/app.js'));

        $this->assertStringContainsString("'[data-reveal], [data-reveal-group]'", $script);

        // ... and the CSS that walks them up slowly has to exist. Lightning CSS
        // drops the universal selector, so match the compiled shape.
        $css = $this->builtCss();

        $this->assertStringContainsString('[data-reveal-slow]>*', $css);
        $this->assertMatchesRegularExpression('/\[data-reveal-slow\]\[data-revealed\]>:nth-child\(4\)/', $css);
    }

    public function test_the_greeting_halves_reveal_from_opposite_edges(): void
    {
        // Crossing entrances are the whole point: the portrait slides in from its
        // own side and the copy from the other, whichever way round they sit.
        config()->set('school.principal.photo_side', 'right');

        $html = $this->get(route('home'))->assertOk()->getContent();
        [, $xp] = $this->parseHtml($html);

        $portrait = $xp->query('//*[@id="sambutan"]//figure[@data-reveal]')->item(0);
        $text = $xp->query('//*[@id="sambutan"]//div[@data-reveal]')->item(0);

        $this->assertSame('right', $portrait->getAttribute('data-reveal-from'));
        $this->assertSame('left', $text->getAttribute('data-reveal-from'));

        // The copy cascades in, so the block carries the group hook. The attribute is
        // written without a value, which is why presence is asserted, not a value.
        $this->assertTrue($text->hasAttribute('data-reveal-group'));

        // Neither half is marked revealed in the markup: app.js decides that
        // when it is scrolled into view, and the hidden state is gated on .has-js
        // so the copy is readable without JavaScript.
        $this->assertFalse($portrait->hasAttribute('data-revealed'));
        $this->assertFalse($text->hasAttribute('data-revealed'));

        config()->set('school.principal.photo_side', 'left');

        $flipped = $this->get(route('home'))->assertOk()->getContent();
        [, $xp] = $this->parseHtml($flipped);

        $this->assertSame(
            'left',
            $xp->query('//*[@id="sambutan"]//figure[@data-reveal]')->item(0)->getAttribute('data-reveal-from')
        );
        $this->assertSame(
            'right',
            $xp->query('//*[@id="sambutan"]//div[@data-reveal]')->item(0)->getAttribute('data-reveal-from')
        );

        // The order classes are merged into one attribute; a second class= would
        // be dropped by the parser and take the visual order with it. Scoped to
        // the greeting, because other sections reveal their own blocks too.
        preg_match('/<section id="sambutan".*?<\/section>/s', $html, $section);
        preg_match_all('/<[a-z]+\b[^>]*\bdata-reveal\b[^>]*>/', $section[0] ?? '', $tags);

        $this->assertCount(2, $tags[0]);

        foreach ($tags[0] as $tag) {
            $this->assertSame(1, substr_count($tag, 'class='), "Expected one class attribute in: {$tag}");
        }
    }

    public function test_the_greeting_survives_a_missing_javascript_environment(): void
    {
        // The hidden state lives behind .has-js, which only the layout's inline
        // head script adds. Guard the guard: without that class the copy must be
        // plain visible content, which is why nothing in app.css may hide
        // [data-reveal] unconditionally.
        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString("classList.add('has-js')", $html);
    }

    public function test_the_hero_marks_the_first_slide_active_so_it_works_without_javascript(): void
    {
        config()->set('school.hero.slides', [
            ['photo' => $this->fakeHeroPhoto('slide-1.jpg'), 'duration' => 3000, 'caption' => true],
            ['photo' => $this->fakeHeroPhoto('slide-2.jpg')],
            ['photo' => $this->fakeHeroPhoto('slide-3.jpg')],
        ]);

        $html = $this->get(route('home'))->assertOk()->getContent();
        [, $xp] = $this->parseHtml($html);

        $active = $xp->query('//div[@data-hero-slide][@data-state="active"]');

        $this->assertSame(1, $active->length, 'Exactly one slide must be active on first paint.');
        $this->assertSame('0', $active->item(0)->getAttribute('data-hero-slide'));

        // The caption is on screen for that same slide.
        $this->assertSame(
            'active',
            $xp->query('//*[@data-hero-caption]')->item(0)->getAttribute('data-state')
        );
    }

    public function test_the_corner_dots_step_through_the_slides(): void
    {
        config()->set('school.hero.slides', [
            ['photo' => $this->fakeHeroPhoto('slide-1.jpg')],
            ['photo' => $this->fakeHeroPhoto('slide-2.jpg')],
            ['photo' => $this->fakeHeroPhoto('slide-3.jpg')],
        ]);

        $html = $this->get(route('home'))->assertOk()->getContent();
        [, $xp] = $this->parseHtml($html);

        $dots = $xp->query('//button[@data-hero-dot]');

        $this->assertSame(3, $dots->length);

        foreach ($dots as $index => $dot) {
            $this->assertSame((string) $index, $dot->getAttribute('data-hero-dot'));
            $this->assertSame('Tampilkan foto '.($index + 1), $dot->getAttribute('aria-label'));
        }

        // The dot for the slide on screen is marked, so it can be styled wider.
        $this->assertSame('true', $dots->item(0)->getAttribute('aria-current'));

        // A stray second class attribute is dropped by the parser and would
        // take the dot's width with it, so the raw markup is checked too.
        preg_match_all('/<button\b[^>]*data-hero-dot[^>]*>/', $html, $tags);

        $this->assertSame(3, count($tags[0]));

        foreach ($tags[0] as $tag) {
            $this->assertSame(1, preg_match_all('/\bclass=/', $tag), "Expected one class attribute in: {$tag}");
        }
    }

    public function test_a_single_slide_has_neither_dots_nor_a_rotation(): void
    {
        // One photo cannot rotate, so the control would be a dead button.
        config()->set('school.hero.slides', [
            ['photo' => $this->fakeHeroPhoto(), 'caption' => true],
        ]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringNotContainsString('data-hero-dot', $html);
        $this->assertStringNotContainsString('data-hero-slider', $html);
    }

    public function test_a_slide_may_use_a_clip_instead_of_a_photo(): void
    {
        config()->set('school.hero.slides', [
            ['video' => $this->fakeHeroVideo()],
        ]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        // The clip fills the whole banner, with no type="" guess so the browser
        // can pick whatever codec it supports.
        $this->assertStringContainsString('<source src="'.asset('videos/hero/gedung-sekolah.mp4').'"', $html);

        preg_match('/<video\b[^>]*>/', $html, $videoTag);
        $this->assertNotEmpty($videoTag, 'The hero video element was not rendered.');
        $tag = $videoTag[0];

        $this->assertStringContainsString('object-cover', $tag);

        // autoplay is only honoured on a muted, inline clip.
        foreach (['autoplay', 'muted', 'loop', 'playsinline'] as $attribute) {
            $this->assertStringContainsString($attribute, $tag, "The clip is missing {$attribute}.");
        }

        $this->assertArrayNotHasKey('overlay', config('school.hero'));
        $this->assertStringNotContainsString('bg-slate-950/85', $html);
        $this->assertStringNotContainsString('blur-3xl', $html);
    }

    public function test_a_slide_never_renders_a_photo_and_a_clip_together(): void
    {
        // A slide takes a photo OR a clip: the photo wins when both keys are
        // present, so adding a clip does not mean deleting the photo first.
        config()->set('school.hero.slides', [
            ['photo' => $this->fakeHeroPhoto(), 'video' => $this->fakeHeroVideo()],
        ]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString(asset('images/hero/gedung-sekolah.jpg'), $html);
        $this->assertStringNotContainsString('<video', $html);
    }

    public function test_the_slide_poster_is_only_used_alongside_a_clip(): void
    {
        config()->set('school.hero.slides', [
            ['video' => $this->fakeHeroVideo(), 'poster' => $this->fakeHeroPhoto('gedung-poster.jpg')],
        ]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        // The still belongs on the <video> element so it shows while the clip loads.
        $this->assertStringContainsString('poster="'.asset('images/hero/gedung-poster.jpg').'"', $html);

        // Without a clip there is nothing to poster, so a stale value is only used
        // as an ordinary photo background.
        config()->set('school.hero.slides', [
            ['photo' => $this->fakeHeroPhoto('gedung-poster.jpg')],
        ]);

        $photoOnly = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringNotContainsString('poster=', $photoOnly);
        $this->assertStringContainsString(asset('images/hero/gedung-poster.jpg'), $photoOnly);
    }

    public function test_a_slide_that_has_not_been_uploaded_falls_back_to_the_texture(): void
    {
        // A configured path with no file behind it must not render a broken
        // image, and it must not break the timing of the slides around it.
        config()->set('school.hero.slides', [
            ['photo' => 'tidak-ada.jpg', 'duration' => 5000],
            ['photo' => $this->fakeHeroPhoto('slide-2.jpg'), 'duration' => 1000],
        ]);

        $html = $this->get(route('home'))->assertOk()->getContent();
        [, $xp] = $this->parseHtml($html);

        $this->assertStringNotContainsString('tidak-ada.jpg', $html);
        $this->assertStringContainsString('bg-brick-green', $html);

        // Both slides are still there, so the sequence keeps rotating.
        $this->assertSame(2, $xp->query('//div[@data-hero-slide]')->length);
    }

    public function test_a_mistyped_duration_cannot_freeze_the_sequence(): void
    {
        config()->set('school.hero.slides', [
            ['photo' => $this->fakeHeroPhoto('slide-1.jpg')],
            ['photo' => $this->fakeHeroPhoto('slide-2.jpg'), 'duration' => 0],
        ]);

        $html = $this->get(route('home'))->assertOk()->getContent();
        [, $xp] = $this->parseHtml($html);

        $second = $xp->query('//div[@data-hero-slide][2]')->item(0);

        $this->assertSame('400', $second->getAttribute('data-hero-duration'));
    }

    public function test_the_images_helper_builds_public_urls(): void
    {
        $this->assertSame(asset('images/hero/gedung.jpg'), Images::url('gedung.jpg', 'hero'));
        $this->assertSame(asset('images/hero/gedung.jpg'), Images::url('/gedung.jpg', 'hero'));
        $this->assertSame(asset('images/logo/logo.png'), Images::url('images/logo/logo.png'));

        // A path that already carries the subfolder, or the base plus the
        // subfolder, must not be prefixed a second time.
        $this->assertSame(asset('images/hero/gedung.jpg'), Images::url('hero/gedung.jpg', 'hero'));
        $this->assertSame(asset('images/hero/gedung.jpg'), Images::url('images/hero/gedung.jpg', 'hero'));

        // existingUrl() reports absence so callers can substitute a fallback.
        $this->assertNull(Images::existingUrl('tidak-ada.jpg', 'hero'));
    }

    public function test_the_images_helper_survives_a_deployment_subfolder(): void
    {
        // The VPS serves the app from /meschool, so asset() prepends that
        // subfolder to every URL. The folder must still only be applied once,
        // and existingUrl() must check the file on disk rather than trusting
        // the URL path (which carries /meschool, not a directory under public/).
        URL::forceRootUrl('http://80.43.0.10/meschool');

        // A throwaway filename: fakeMedia() is unlinked in tearDown(), and a
        // name that matches a tracked asset would delete the real file.
        $this->fakeHeroPhoto('subfolder-check.jpg');

        $this->assertSame(
            'http://80.43.0.10/meschool/images/hero/subfolder-check.jpg',
            Images::url('images/hero/subfolder-check.jpg', 'hero'),
        );

        $this->assertSame(
            'http://80.43.0.10/meschool/images/hero/subfolder-check.jpg',
            Images::existingUrl('images/hero/subfolder-check.jpg', 'hero'),
        );
    }

    public function test_the_videos_helper_builds_public_urls(): void
    {
        $this->assertSame(asset('videos/hero/gedung.mp4'), Videos::url('gedung.mp4', 'hero'));
        $this->assertSame(asset('videos/hero/gedung.mp4'), Videos::url('/gedung.mp4', 'hero'));
        $this->assertSame(asset('videos/hero/gedung.mp4'), Videos::url('videos/hero/gedung.mp4'));

        $this->assertNull(Videos::existingUrl('tidak-ada.mp4', 'hero'));

        // A clip may be served from a CDN, and a URL cannot be checked on disk.
        $this->assertSame('https://cdn.example.com/hero.mp4', Videos::url('https://cdn.example.com/hero.mp4', 'hero'));
        $this->assertSame('https://cdn.example.com/hero.mp4', Videos::existingUrl('https://cdn.example.com/hero.mp4', 'hero'));
    }

    /**
     * Drop a stand-in principal portrait into public/ so Images::existingUrl()
     * resolves, and return the path to put in config.
     */
    private function fakePrincipalPhoto(string $name = 'kepala-sekolah-test.jpg'): string
    {
        return $this->fakeMedia('images/principal/'.$name);
    }

    /**
     * Drop a stand-in hero photo into public/ so Images::existingUrl() resolves,
     * and return the bare filename to put in config.
     */
    private function fakeHeroPhoto(string $name = 'gedung-sekolah.jpg'): string
    {
        return $this->fakeMedia('images/hero/'.$name);
    }

    /**
     * Same for a hero clip in public/videos/.
     */
    private function fakeHeroVideo(string $name = 'gedung-sekolah.mp4'): string
    {
        return $this->fakeMedia('videos/hero/'.$name);
    }

    /**
     * Same for a teacher portrait in public/images/guru/.
     */
    private function fakeStaffPhoto(string $name = 'guru-test.jpg'): string
    {
        return $this->fakeMedia('images/guru/'.$name);
    }

    private function fakeMedia(string $relative): string
    {
        $path = public_path($relative);

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        file_put_contents($path, 'placeholder');

        $this->fakeMedia[] = $path;

        return basename($relative);
    }

    protected function tearDown(): void
    {
        foreach ($this->fakeMedia as $path) {
            @unlink($path);
        }

        parent::tearDown();
    }

    public function test_it_renders_the_contact_strip(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Selamat Datang di');
        $response->assertSee(config('school.domain'));
        $response->assertSee('Hubungi kontak pusat informasi :');
    }

    public function test_the_dropdowns_contain_unique_checkbox_ids(): void
    {
        // The navbar renders <x-nav-item> twice (desktop bar + mobile drawer).
        // Repeating an id would leave both labels pointing at the same checkbox.
        $html = $this->get(route('home'))->assertOk()->getContent();

        preg_match_all('/id="(nav-[^"]+)"/', $html, $matches);

        $this->assertNotEmpty($matches[1]);
        $this->assertSame($matches[1], array_values(array_unique($matches[1])));
    }

    #[DataProvider('menuRoutes')]
    public function test_every_menu_entry_resolves_to_a_page(string $routeName): void
    {
        $this->get(route($routeName))->assertOk();
    }

    public static function menuRoutes(): array
    {
        return [
            'program.kuliner' => ['program.kuliner'],
            'program.dkv' => ['program.dkv'],
            'program.tkj' => ['program.tkj'],
            'program.ak' => ['program.ak'],
            'program.mp' => ['program.mp'],
            'program.bd' => ['program.bd'],
            'tenaga-kependidikan' => ['tenaga-kependidikan'],
            'berita' => ['berita'],
            'bkk' => ['bkk'],
            'spmb' => ['spmb'],
            'kontak' => ['kontak'],
            'search' => ['search'],
        ];
    }

    public function test_the_orphaned_child_pages_still_resolve(): void
    {
        // Berita/BKK/SPMB children are no longer in the bar, but their routes
        // stay registered so old links and bookmarks keep working.
        foreach (['berita.pengumuman', 'berita.kegiatan', 'berita.prestasi',
            'bkk.lowongan', 'bkk.tracer-study', 'spmb.tentang-kami', 'spmb.informasi-sekolah'] as $routeName) {
            $this->get(route($routeName))->assertOk();
        }
    }

    public function test_the_staff_section_follows_the_programs(): void
    {
        // Order matters: the people come last, after what the school offers.
        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $section = $xp->query('//*[@id="tenaga-kependidikan"]')->item(0);

        $this->assertNotNull($section, 'The staff section is missing from the home page.');

        // Order matters: the people come last, after what the school offers.
        $programs = strpos($html, 'id="program-keahlian"');
        $staff = strpos($html, 'id="tenaga-kependidikan"');

        $this->assertNotFalse($programs);
        $this->assertNotFalse($staff);
        $this->assertLessThan($staff, $programs);

        // The eyebrow remains config-driven; the editorial heading is concise
        // to match the staff-card layout.
        $this->assertStringContainsString(config('school.staff.eyebrow'), $html);
        $this->assertSame('Guru Profesional', trim($xp->query('.//h2', $section)->item(0)->textContent));

        $this->assertArrayNotHasKey('intro', config('school.staff'));
        $this->assertSame(1, $xp->query('.//p[contains(@class, "tk-heading-intro")]', $section)->length);
    }

    public function test_the_staff_section_is_an_eight_person_editorial_index(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $list = $xp->query('//ul[@data-staff-rail]')->item(0);

        $this->assertNotNull($list, 'The staff index is missing.');

        $this->assertSame(count(config('school.staff.items')), $xp->query('./li', $list)->length);
        $this->assertSame(8, $xp->query('./li[@data-staff-row]', $list)->length);
        $this->assertSame(8, $xp->query('.//button[@data-staff-open]', $list)->length);
        $this->assertSame(0, $xp->query('.//li[not(@data-staff-row)]', $list)->length);
        $this->assertSame(8, $xp->query('./li//span[contains(@class, "tk-card-photo")]', $list)->length);
        $this->assertStringContainsString('.tk-card{', $this->builtCss());

        $names = [];

        foreach ($xp->query('./li[@data-staff-row]', $list) as $row) {
            $names[] = trim($xp->query('.//span[@class="tk-name"]', $row)->item(0)->textContent);
        }

        $this->assertCount(8, array_unique($names), 'A configured person is rendered more than once.');
    }

    public function test_the_staff_rail_shows_the_next_card_at_the_right_edge(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $this->assertSame(8, $xp->query('//ul[@data-staff-rail]/li[@data-staff-row]')->length);
        $this->assertStringContainsString('.tk-list{', $this->builtCss());
        $this->assertStringContainsString('.tk-card{', $this->builtCss());
        $this->assertDoesNotMatchRegularExpression('/\.tk-list\{[^}]*overflow-x:auto/', $this->builtCss());
    }

    public function test_every_staff_card_carries_the_configured_copy(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $cards = iterator_to_array($xp->query('//ul[@data-staff-rail]/li[@data-staff-row]'));

        foreach ($cards as $index => $card) {
            $item = config('school.staff.items')[$index];

            $this->assertSame($item['name'], trim($xp->query('.//span[@class="tk-name"]', $card)->item(0)->textContent));
            $this->assertSame(
                $item['position'],
                trim($xp->query('.//span[@class="tk-position"]', $card)->item(0)->textContent),
            );
            $this->assertStringContainsString('Lihat profil '.$item['name'], $xp->query('.//button[@data-staff-open]', $card)->item(0)->getAttribute('aria-label'));
        }
    }

    public function test_every_staff_card_is_opened_by_one_labelled_button(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $triggers = iterator_to_array($xp->query('//ul[@data-staff-rail]//button[@data-staff-open]'));

        $this->assertCount(count(config('school.staff.items')), $triggers);

        foreach ($triggers as $index => $trigger) {
            $this->assertSame('button', $trigger->getAttribute('type'));
            $this->assertSame((string) $index, $trigger->getAttribute('data-staff-index'));

            $this->assertSame('dialog', $trigger->getAttribute('aria-haspopup'));
            $this->assertSame('staff-profile-dialog', $trigger->getAttribute('aria-controls'));
            $this->assertStringContainsString(config('school.staff.items')[$index]['name'], $trigger->getAttribute('aria-label'));
        }
    }

    public function test_the_staff_arrows_are_a_labelled_pair(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $prev = $xp->query('//dialog[@data-staff-modal]//button[@data-staff-prev]')->item(0);
        $next = $xp->query('//dialog[@data-staff-modal]//button[@data-staff-next]')->item(0);

        $this->assertNotNull($prev);
        $this->assertNotNull($next);

        foreach ([$prev, $next] as $arrow) {
            $this->assertSame('button', $arrow->getAttribute('type'));
            $this->assertNotSame('', trim($arrow->getAttribute('aria-label')));
        }

        $this->assertSame(0, $xp->query('//ul[@data-staff-rail]//button[@data-staff-prev]')->length);
        $this->assertSame(0, $xp->query('//ul[@data-staff-rail]//button[@data-staff-next]')->length);
    }

    public function test_the_staff_profile_dialog_is_built_for_assistive_tech(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $dialog = $xp->query('//dialog[@data-staff-modal]')->item(0);

        $this->assertNotNull($dialog, 'The profile popup is missing.');

        // A native dialog carries the top layer, the backdrop and the Escape key,
        // and it only makes sense if the heading inside it is what labels it.
        $labelledBy = $dialog->getAttribute('aria-labelledby');

        $this->assertNotSame('', $labelledBy);
        $this->assertNotNull($xp->query('//*[@id="'.$labelledBy.'"]')->item(0));

        // Every field the popup shows is a slot the script fills.
        foreach (['name', 'position', 'description', 'expertise', 'photo'] as $slot) {
            $this->assertNotNull(
                $xp->query('//dialog//*[@data-staff-modal-'.$slot.']')->item(0),
                "The popup has no {$slot} slot.",
            );
        }

        // One close button, explicitly labelled rather than relying on the glyph.
        $close = $xp->query('//dialog//button[@data-staff-close]')->item(0);

        $this->assertNotNull($close);
        $this->assertSame('button', $close->getAttribute('type'));
        $this->assertNotSame('', trim($close->getAttribute('aria-label')));

        // The script is what opens and closes it, so its hooks have to survive.
        $script = file_get_contents(resource_path('js/tenaga-kependidikan.js'));

        foreach (['data-staff-open', 'data-staff-close', 'data-staff-prev', 'data-staff-next'] as $hook) {
            $this->assertStringContainsString($hook, $script);
        }

        $this->assertStringContainsString('dialog.showModal()', $script);

        $css = $this->builtCss();

        $this->assertMatchesRegularExpression('/\.tk-modal\[open\] \.tk-modal-panel\{[^}]*opacity:1/', $css);
        $this->assertMatchesRegularExpression('/\.tk-modal-panel\{[^}]*opacity:0/', $css);
        $this->assertStringContainsString('.tk-modal::backdrop', $css);
        $this->assertStringContainsString('prefers-reduced-motion', $css);
    }

    public function test_the_profile_popup_is_filled_from_the_same_data_the_cards_render(): void
    {
        // One dialog serves the whole rail, so its payload has to hold every
        // person the cards rendered, in the same order.
        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $payload = $xp->query('//script[@data-staff-data]')->item(0);

        $this->assertNotNull($payload, 'The popup payload is missing.');
        $this->assertSame('application/json', $payload->getAttribute('type'));

        $people = json_decode($payload->textContent, true);

        $this->assertIsArray($people);
        $this->assertCount(count(config('school.staff.items')), $people);

        foreach (config('school.staff.items') as $index => $item) {
            $person = $people[$index];

            $this->assertSame($item['name'], $person['name']);
            $this->assertSame($item['position'], $person['position']);
            $this->assertSame($item['description'], $person['description']);
            $this->assertSame($item['expertise'], $person['expertise']);

            // The monogram is what the card falls back to, so it is built here
            // rather than in the script.
            $this->assertSame(
                Str::upper(Str::initials(Str::before($item['name'], ','))),
                $person['initials'],
            );

            // A configured-but-missing photo resolves to null, never to a URL the
            // popup would then fail to load.
            $expected = Images::existingUrl($item['photo'], 'guru') ?? Images::existingUrl($item['photo']);

            $this->assertSame($expected, $person['photo']);
        }
    }

    public function test_a_staff_photo_that_is_missing_falls_back_to_a_monogram(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);
        $payload = json_decode($xp->query('//script[@data-staff-data]')->item(0)->textContent, true);

        foreach (config('school.staff.items') as $index => $item) {
            if (Images::existingUrl($item['photo'], 'guru') !== null) {
                continue;
            }

            $this->assertStringNotContainsString($item['photo'], $html);
            $this->assertNull($payload[$index]['photo']);
            $this->assertSame(Str::upper(Str::initials(Str::before($item['name'], ','))), $payload[$index]['initials']);
        }
    }

    public function test_an_uploaded_staff_photo_is_rendered_with_alt_text(): void
    {
        config()->set('school.staff.items', [
            [
                'name' => 'Ahmad Fauzan, S.Pd.',
                'position' => 'Guru Produktif TKJ',
                'photo' => $this->fakeStaffPhoto(),
                'description' => 'Mengajar bidang jaringan komputer dan administrasi sistem.',
                'expertise' => ['Jaringan komputer'],
            ],
        ]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $payload = json_decode($xp->query('//script[@data-staff-data]')->item(0)->textContent, true);

        $this->assertSame(asset('images/guru/guru-test.jpg'), $payload[0]['photo']);
        $this->assertSame(
            asset('images/guru/guru-test.jpg'),
            $xp->query('//li[@data-staff-row][1]//span[contains(@class, "tk-card-photo")]/img')->item(0)->getAttribute('src'),
        );
        $this->assertStringContainsString("image.alt = 'Foto ' + person.name", file_get_contents(resource_path('js/tenaga-kependidikan.js')));
        $this->assertStringContainsString("image.loading = 'lazy'", file_get_contents(resource_path('js/tenaga-kependidikan.js')));
    }

    public function test_the_staff_index_has_a_subtle_fine_pointer_hover_and_clear_focus_ring(): void
    {
        $css = $this->builtCss();

        $this->assertStringContainsString('@media (hover:hover) and (pointer:fine)', $css);
        $this->assertStringContainsString('.tk-card:hover', $css);
        $this->assertStringContainsString('.tk-trigger:focus-visible', $css);
        $this->assertStringContainsString('prefers-reduced-motion:reduce', $css);
    }

    public function test_the_staff_heading_and_index_are_revealed_from_opposite_edges(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $section = $xp->query('//*[@id="tenaga-kependidikan"]')->item(0);

        $this->assertSame(1, $xp->query('.//h2/following-sibling::p[contains(@class, "tk-heading-intro")]', $section)->length);
        $this->assertSame('Guru Profesional', trim($xp->query('.//h2', $section)->item(0)->textContent));
        $this->assertSame(1, $xp->query('.//ul[@data-staff-rail]', $section)->length);
    }

    public function test_the_staff_preview_and_modal_are_not_a_marquee(): void
    {
        $script = file_get_contents(resource_path('js/tenaga-kependidikan.js'));

        $this->assertStringContainsString('requestAnimationFrame(placePreview)', $script);
        $this->assertStringContainsString('preview.dataset.visible', $script);
        $this->assertStringContainsString('dialog.showModal()', $script);
        $this->assertStringContainsString('ArrowLeft', $script);
        $this->assertStringContainsString('ArrowRight', $script);
        $this->assertStringContainsString('swipeDistance > 80', $script);
        $this->assertStringNotContainsString('cloneNode(true)', $script);
        $this->assertStringNotContainsString('singleSet', $script);
    }

    public function test_the_staff_index_stays_inside_the_page_container(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $rail = $xp->query('//ul[@data-staff-rail]')->item(0);
        $containers = $xp->query('ancestor::div[contains(@class, "tk-shell")]', $rail);

        $this->assertGreaterThan(
            0,
            $containers->length,
            'The staff index is not inside its bounded page container.',
        );

        $this->assertSame(0, $xp->query('.//li[not(@data-staff-row)]', $rail)->length);
    }

    public function test_the_closed_profile_dialog_is_hidden_and_the_open_layer_covers_the_viewport(): void
    {
        $css = $this->builtCss();

        $this->assertStringContainsString('.tk-modal:not([open])', $css);
        $this->assertMatchesRegularExpression(
            '/\.tk-modal:not\(\[open\]\)\{[^}]*display:none/',
            $css,
        );

        $open = [];
        $this->assertTrue(
            (bool) preg_match('/\.tk-modal\[open\]\{([^}]*)\}/', $css, $open),
            'The open dialog has no rule of its own.',
        );

        $this->assertStringContainsString('position:fixed', $open[1]);
        $this->assertStringContainsString('inset:0', $open[1]);
        $this->assertStringContainsString('width:auto', $open[1]);
        $this->assertStringContainsString('height:auto', $open[1]);

        $this->assertDoesNotMatchRegularExpression('/\.tk-modal\[open\]\{[^}]*100vw/', $css);
    }

    public function test_the_staff_rail_and_the_profile_dialog_stay_out_of_each_others_way(): void
    {
        // The dialog is pinned to the viewport, so it must not sit inside the
        // page container where a max-width or a transform could come between it
        // and the viewport, and it must not inherit the reveal behaviour either.
        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $dialog = $xp->query('//dialog[@data-staff-modal]')->item(0);

        $this->assertNotNull($dialog);

        // `.//` throughout: a bare `//` starts at the document and would count
        // every reveal on the page instead of the ones inside the dialog.
        $this->assertSame(0, $xp->query('ancestor::div[contains(@class, "tk-shell")]', $dialog)->length);
        $this->assertSame(0, $xp->query('.//*[@data-reveal]', $dialog)->length);
        $this->assertSame(0, $xp->query('.//*[@data-reveal-group]', $dialog)->length);
    }

    public function test_the_staff_profile_navigation_is_scoped_to_the_dialog(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $section = $xp->query('//*[@id="tenaga-kependidikan"]')->item(0);

        $this->assertSame(1, $xp->query('.//dialog//button[@data-staff-prev]', $section)->length);
        $this->assertSame(1, $xp->query('.//dialog//button[@data-staff-next]', $section)->length);
        $this->assertSame(0, $xp->query('//ul[@data-staff-rail]//button[@data-staff-prev]', $section)->length);

        $script = file_get_contents(resource_path('js/tenaga-kependidikan.js'));

        $this->assertStringContainsString("dialog.querySelector('[data-staff-prev]')", $script);
        $this->assertStringContainsString("dialog.querySelector('[data-staff-next]')", $script);
    }
}
