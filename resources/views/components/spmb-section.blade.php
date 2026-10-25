@php
    use App\Support\Images;

    $copy = config('school.spmb');

    // The band is a photo, and the only school photo that ships with the repo
    // is the one the hero serves, so it doubles as the SPMB background until a
    // dedicated upload lands. Resolved through existingUrl() with the same
    // base-prefixed fallback as the hero, so a missing file renders the brick
    // gradient below instead of a broken layer.
    $path = (string) ($copy['bg_photo'] ?? '');
    $bg = $path !== ''
        ? (Images::existingUrl($path, 'spmb') ?? Images::existingUrl($path))
        : null;
@endphp

{{-- The SPMB recruitment banner that closes the home page: a full-width photo
     band sitting clear of the BKK grid above. The photo is a plain cover — no
     pan or zoom — and the config overlay plus the scrim in spmb-section.css do
     the contrast work. The copy cascades in once through the shared reveal
     observer, then stays put; prefers-reduced-motion gets the same instant
     reveal as everywhere else.

     Like the hero, the band pins to the top of the viewport (sticky) on its
     way in and sits behind -z-10, so the block that follows scrolls up and
     covers the photo smoothly. --}}
<section
    id="spmb"
    aria-labelledby="spmb-title"
    class="spmb-banner spmb-section relative isolate sticky top-0 -z-10 min-h-[500px] overflow-hidden bg-school-950"
>
    @if ($bg)
        <div
            aria-hidden="true"
            data-spmb-bg
            class="absolute inset-0 -z-20 bg-cover bg-center"
            style="background-image:url('{{ $bg }}')"
        ></div>
    @else
        <div aria-hidden="true" class="absolute inset-0 -z-20 bg-gradient-to-br from-school-700 via-school-800 to-school-950"></div>
    @endif

    <div
        aria-hidden="true"
        data-spmb-overlay
        class="absolute inset-0 -z-10 {{ $copy['overlay'] }}"
    ></div>

    <div class="spmb-shell">
        <div class="spmb-copy" data-reveal-group>
            <p class="spmb-eyebrow">
                <span aria-hidden="true" class="spmb-eyebrow-rule"></span>
                {{ $copy['eyebrow'] }}
            </p>

            <h2 id="spmb-title" class="spmb-title">{{ $copy['title'] }}</h2>

            <p class="spmb-year">
                <x-icon name="calendar" class="spmb-year-icon" />
                {{ $copy['subtitle'] }}
            </p>

            <p class="spmb-desc">{{ $copy['description'] }}</p>

            <div class="spmb-actions">
                <a href="{{ route('spmb') }}" class="spmb-btn spmb-btn--primary">
                    {{ $copy['button_label'] }}
                    <x-icon name="arrow-right" class="spmb-btn-icon" />
                </a>

                <a href="{{ route('spmb') }}" class="spmb-btn spmb-btn--ghost">
                    {{ $copy['link_label'] }}
                    <x-icon name="chevron-right" class="spmb-btn-icon" />
                </a>
            </div>
        </div>
    </div>
</section>
