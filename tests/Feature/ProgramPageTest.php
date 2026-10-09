<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProgramPageTest extends TestCase
{
    /**
     * Every slug in the catalogue — the six navbar dropdown items AND the four
     * home-page cards — resolves to a page carrying that program's own copy
     * from config, never the generic placeholder.
     */
    public function test_every_program_route_renders_its_configured_copy(): void
    {
        $catalog = config('school.program_catalog.items');

        $this->assertGreaterThanOrEqual(9, count($catalog));

        foreach ($catalog as $slug => $item) {
            $html = $this->get(route('program.'.$slug))->assertOk()->getContent();

            $this->assertStringContainsString($item['name'], $html);
            $this->assertStringContainsString($item['tagline'], $html);
            $this->assertStringContainsString($item['description'], $html);
        }
    }

    /**
     * The two lists that are the meat of the page — what is studied and where
     * it leads — render every configured entry.
     */
    public function test_the_page_lists_competencies_and_careers_from_config(): void
    {
        $item = config('school.program_catalog.items.kuliner');

        $html = $this->get(route('program.kuliner'))->assertOk()->getContent();

        foreach ($item['competencies'] as $competency) {
            $this->assertStringContainsString($competency, $html);
        }

        foreach ($item['careers'] as $career) {
            $this->assertStringContainsString($career, $html);
        }
    }

    /**
     * The pill nav under the hero links every other programme and marks the
     * one on screen as current, so a programme page never dead-ends.
     */
    public function test_the_pill_nav_marks_the_current_program_and_links_the_rest(): void
    {
        $html = $this->get(route('program.tkj'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $pills = $xp->query('//*[@data-program-pills]');
        $this->assertSame(1, $pills->length);

        $current = $xp->query('//*[@data-program-pills]/a[@aria-current="page"]');
        $this->assertSame(1, $current->length, 'Exactly one pill should mark the program on screen.');

        $activeClass = $current->item(0)->getAttribute('class');
        $this->assertStringContainsString('bg-gold-500', $activeClass);

        foreach (config('school.program_catalog.items') as $slug => $item) {
            $query = '//*[@data-program-pills]/a[@href="'.route('program.'.$slug).'"]';
            $this->assertSame(1, $xp->query($query)->length, "The pill for {$slug} is missing.");
            $this->assertStringContainsString($item['name'], (string) $xp->query($query)->item(0)->textContent);
        }
    }

    /**
     * The dark band that closes the page points at the two real next steps.
     */
    public function test_the_page_closes_with_the_next_step_actions(): void
    {
        $html = $this->get(route('program.mp'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $this->assertSame(1, $xp->query('//*[@data-program-cta]//a[@href="'.route('kontak').'"]')->length);
        $this->assertSame(1, $xp->query('//*[@data-program-cta]//a[@href="'.route('bkk').'"]')->length);
        $this->assertStringContainsString('Tertarik masuk MP?', $html);
    }
}
