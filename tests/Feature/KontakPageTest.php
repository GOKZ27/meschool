<?php

namespace Tests\Feature;

use Tests\TestCase;

class KontakPageTest extends TestCase
{
    public function test_it_renders_every_contact_channel(): void
    {
        $response = $this->get(route('kontak'));

        $response->assertOk();
        $response->assertSee('Kontak');
        $response->assertSee(config('school.contact.whatsapp'));
        $response->assertSee(config('school.contact.phone'));
        $response->assertSee(config('school.contact.email'));
        $response->assertSee(config('school.contact.address'));
    }

    public function test_the_contact_channels_link_out_correctly(): void
    {
        $response = $this->get(route('kontak'));

        $response->assertOk();
        $response->assertSee(config('school.contact.whatsapp_link'), escape: false);
        $response->assertSee(config('school.contact.phone_link'), escape: false);
        $response->assertSee('mailto:'.config('school.contact.email'), escape: false);
    }

    public function test_it_shares_the_site_chrome(): void
    {
        $html = $this->get(route('kontak'))->assertOk()->getContent();

        $this->assertStringContainsString('class="nav-root', $html);
        $this->assertStringContainsString('Selamat Datang di', $html);
    }

    public function test_a_dropdown_child_marks_its_own_link_as_current(): void
    {
        // Visiting "Program Keahlian / TKJ" should mark the TKJ link (desktop
        // mega panel + mobile drawer) as aria-current, while sibling entries
        // stay unmarked and the parent only gets its own active styling.
        $html = $this->get(route('program.tkj'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $current = $xp->query('//nav//a[@aria-current="page"]');

        $this->assertNotFalse($current);
        $this->assertGreaterThan(0, $current->length);

        foreach ($current as $link) {
            $this->assertSame(route('program.tkj'), $link->getAttribute('href'));
        }

        // Four instances: nav-item renders once in the desktop bar and once in
        // the mobile drawer, and each renders a desktop and a mobile panel.
        $this->assertCount(4, iterator_to_array($current));

        // The parent label is not itself the current page.
        $this->assertSame(0, $xp->query('//nav//a[@href="#"][@aria-current]')->length);
    }
}
