@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'type' => 'website',
    'robots' => 'index, follow, max-image-preview:large',
    'canonical' => null,
])

@php
    use App\Support\Images;
    use Illuminate\Support\Str;

    $pageTitle = $title ?: config('school.name');

    // One meta description for the whole page. Page copy is plain text, so
    // stripping tags keeps a stray <strong> from leaking into the snippet.
    $metaDescription = Str::limit(strip_tags((string) ($description ?: config('school.description'))), 200, '');

    $canonicalUrl = $canonical ?: url()->current();
    $ogType = $type ?: 'website';

    // One share image for the site: the page's own image when it has one,
    // otherwise the first hero photo, otherwise the logo. A missing file
    // resolves to null so a crawler is never pointed at a 404.
    $shareImage = $image;

    if (! $shareImage) {
        $heroPath = (string) (config('school.hero.slides.0.photo') ?? '');
        $shareImage = $heroPath !== '' ? Images::existingUrl($heroPath, 'hero') : null;
    }

    if (! $shareImage) {
        $logoPath = (string) config('school.logo.path');
        $shareImage = $logoPath !== '' ? Images::existingUrl($logoPath, 'logo') : null;
    }

    if ($shareImage && ! Str::startsWith($shareImage, ['http://', 'https://'])) {
        $shareImage = url($shareImage);
    }

    $logoPath = (string) config('school.logo.path');
    $logoUrl = $logoPath !== '' ? Images::existingUrl($logoPath, 'logo') : null;

    // Site-wide structured data: the school itself plus the site node that
    // advertises the news search as a sitelinks search box.
    $organization = [
        '@type' => 'EducationalOrganization',
        '@id' => url('/').'#organization',
        'name' => config('school.name'),
        'alternateName' => config('school.short_name'),
        'url' => url('/'),
        'description' => config('school.description'),
        'email' => config('school.contact.email'),
        'telephone' => config('school.contact.phone'),
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => config('school.contact.address'),
            'addressCountry' => 'ID',
        ],
        'sameAs' => collect(config('school.socials'))->pluck('url')->filter()->values()->all(),
    ];

    if ($logoUrl) {
        $organization['logo'] = $logoUrl;
    }

    if ($shareImage) {
        $organization['image'] = $shareImage;
    }

    $website = [
        '@type' => 'WebSite',
        '@id' => url('/').'#website',
        'url' => url('/'),
        'name' => config('school.name'),
        'inLanguage' => 'id-ID',
        'publisher' => ['@id' => url('/').'#organization'],
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => route('berita').'?q={search_term_string}',
            ],
            'query-input' => 'required name=search_term_string',
        ],
    ];

    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => [$organization, $website],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $pageTitle }}</title>

        <meta name="description" content="{{ $metaDescription }}">
        <meta name="robots" content="{{ $robots }}">
        <link rel="canonical" href="{{ $canonicalUrl }}">

        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        @if ($logoUrl)
            <link rel="apple-touch-icon" href="{{ $logoUrl }}">
        @endif

        {{-- Open Graph --}}
        <meta property="og:type" content="{{ $ogType }}">
        <meta property="og:site_name" content="{{ config('school.name') }}">
        <meta property="og:locale" content="id_ID">
        <meta property="og:title" content="{{ $pageTitle }}">
        <meta property="og:description" content="{{ $metaDescription }}">
        <meta property="og:url" content="{{ $canonicalUrl }}">
        @if ($shareImage)
            <meta property="og:image" content="{{ $shareImage }}">
            <meta property="og:image:alt" content="{{ config('school.name') }}">
        @endif

        {{-- Twitter / X --}}
        <meta name="twitter:card" content="{{ $shareImage ? 'summary_large_image' : 'summary' }}">
        <meta name="twitter:title" content="{{ $pageTitle }}">
        <meta name="twitter:description" content="{{ $metaDescription }}">
        @if ($shareImage)
            <meta name="twitter:image" content="{{ $shareImage }}">
        @endif

        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

        {{-- Set before first paint so scroll-reveal copy is never painted
             visible and then hidden again by app.css. --}}
        <script>document.documentElement.classList.add('has-js');</script>

        @fonts

        {{-- Guarded so the page still renders before `npm run build` has ever run. --}}
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-white font-sans text-slate-800 antialiased">
        {{ $slot }}
        <x-footer />
    </body>
</html>
