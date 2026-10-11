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
     pan or zoom — and the dark overlay in config does the contrast work. The
     copy cascades in once through the shared reveal observer, then stays put;
     prefers-reduced-motion gets the same instant reveal as everywhere else.

     Like the hero, the band pins to the top of the viewport (sticky) on its
     way in and sits behind -z-10, so the block that follows scrolls up and
     covers the photo smoothly. --}}
<section
    id="spmb"
    class="relative isolate sticky top-0 -z-10 flex h-svh min-h-[500px] items-center overflow-hidden bg-school-950 py-20 sm:py-24 lg:py-32"
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

    <div class="mx-auto w-full max-w-4xl px-4 text-center sm:px-6" data-reveal-group>
        <p class="flex items-center justify-center gap-3 text-xs font-semibold tracking-[0.18em] text-gold-400 uppercase">
            <span aria-hidden="true" class="h-px w-10 bg-gold-500"></span>
            {{ $copy['eyebrow'] }}
            <span aria-hidden="true" class="h-px w-10 bg-gold-500"></span>
        </p>

        <h2 class="mt-6 text-3xl leading-tight font-extrabold tracking-tight text-balance text-white sm:text-4xl lg:text-5xl">
            {{ $copy['title'] }}
        </h2>

        <p class="mt-3 text-lg font-semibold tracking-wide text-gold-300 sm:text-xl">
            {{ $copy['subtitle'] }}
        </p>

        <p class="mx-auto mt-6 max-w-[50rem] text-base leading-relaxed text-school-100/90 sm:text-lg">
            {{ $copy['description'] }}
        </p>

        <div class="mt-10">
            <a
                href="{{ route('spmb') }}"
                class="group inline-flex items-center gap-2 rounded-full bg-gold-500 px-8 py-3.5 text-sm font-bold tracking-wide text-school-950 uppercase shadow-lg shadow-school-950/30 transition hover:-translate-y-0.5 hover:bg-gold-400 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-white"
            >
                {{ $copy['button_label'] }}
                <x-icon name="arrow-right" class="size-4 transition group-hover:translate-x-1" />
            </a>

            <a
                href="{{ route('spmb') }}"
                class="mt-6 inline-flex items-center gap-1.5 text-sm font-medium text-white/85 transition hover:text-white"
            >
                {{ $copy['link_label'] }}
                <x-icon name="chevron-right" class="size-4 text-gold-400" />
            </a>
        </div>
    </div>
</section>