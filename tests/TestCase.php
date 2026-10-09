<?php

namespace Tests;

use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    // Every page that queries the news archive needs the schema, and the
    // suite runs against an in-memory database that starts empty each run.
    use RefreshDatabase;

    /**
     * Build an XPath over the rendered HTML.
     *
     * libxml's HTML parser predates HTML5, so it warns about tags like
     * <header> or <main>. Those warnings are noise here — we only query the
     * DOM — so they are suppressed instead of failing the request.
     *
     * @return array{0: DOMDocument, 1: DOMXPath}
     */
    protected function parseHtml(string $html): array
    {
        $dom = new DOMDocument;

        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $this->assertNotNull($dom->documentElement, 'Rendered HTML could not be parsed.');

        return [$dom, new DOMXPath($dom)];
    }
}
