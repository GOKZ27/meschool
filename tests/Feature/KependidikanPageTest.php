<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use Tests\TestCase;

class KependidikanPageTest extends TestCase
{
    /** @var list<string> stand-in media written for a test, removed in tearDown */
    private array $fakeMedia = [];

    /**
     * The page is the whole staff list: heading copy and every configured
     * member, including the expertise chips the home rail hides in its popup.
     */
    public function test_the_page_renders_every_configured_member(): void
    {
        $html = $this->get(route('tenaga-kependidikan'))->assertOk()->getContent();

        $this->assertStringContainsString(config('school.staff.title'), $html);

        [, $xp] = $this->parseHtml($html);
        $this->assertSame(count(config('school.staff.items')), $xp->query('//*[@data-staff-grid]/li')->length);

        // Expertise chips are compared through the DOM's decoded textContent,
        // because Blade escapes apostrophes ("Al-Qur'an dan Hadis") in the raw
        // markup and a raw-string contains check would never match.
        $chips = [];

        foreach ($xp->query('//*[@data-staff-grid]//li[contains(@class, "rounded-full")]') as $chip) {
            $chips[] = trim($chip->textContent);
        }

        foreach (config('school.staff.items') as $person) {
            $this->assertStringContainsString($person['name'], $html);
            $this->assertStringContainsString($person['position'], $html);
            $this->assertStringContainsString($person['description'], $html);

            foreach ($person['expertise'] ?? [] as $skill) {
                $this->assertContains($skill, $chips);
            }
        }
    }

    /**
     * The configured portraits do not exist on disk, so the page must render a
     * monogram from the name instead of a broken img — the same fallback the
     * home rail uses.
     */
    public function test_missing_portraits_become_monograms_not_broken_images(): void
    {
        $html = $this->get(route('tenaga-kependidikan'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $this->assertSame(0, $xp->query('//*[@data-staff-grid]//img')->length);

        $expected = Str::upper(
            Str::initials(Str::before(config('school.staff.items.0.name'), ','))
        );

        $this->assertSame(
            1,
            $xp->query('//*[@data-staff-grid]//span[normalize-space(.)="'.$expected.'"]')->length,
            'The first member should fall back to a monogram built from the name.'
        );
    }

    /**
     * The day a portrait actually lands in public/images/guru/, it renders as
     * a lazy image with a proper alt — not a monogram.
     */
    public function test_an_uploaded_portrait_renders_as_an_image(): void
    {
        config()->set('school.staff.items.0.photo', 'guru-test.jpg');
        $this->fakeMedia('images/guru/guru-test.jpg');

        $html = $this->get(route('tenaga-kependidikan'))->assertOk()->getContent();

        [, $xp] = $this->parseHtml($html);

        $photo = $xp->query('//*[@data-staff-grid]//img[@src="'.asset('images/guru/guru-test.jpg').'"]');
        $this->assertSame(1, $photo->length);
        $this->assertSame('Foto Ahmad Fauzan, S.Pd.', $photo->item(0)->getAttribute('alt'));
        $this->assertSame('lazy', $photo->item(0)->getAttribute('loading'));
    }

    private function fakeMedia(string $relative): void
    {
        $path = public_path($relative);

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        file_put_contents($path, 'placeholder');

        $this->fakeMedia[] = $path;
    }

    protected function tearDown(): void
    {
        foreach ($this->fakeMedia as $path) {
            @unlink($path);
        }

        parent::tearDown();
    }
}
