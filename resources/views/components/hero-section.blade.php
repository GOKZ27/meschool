@php
    use App\Support\Images;
    use App\Support\Videos;

    $hero = config('school.hero');

    // Every slide is resolved up front. A photo or a clip per slide, never both,
    // and a file that has not been uploaded yet resolves to null so the slide
    // falls back to the brick texture instead of rendering a broken layer.
    $slides = [];

    foreach ($hero['slides'] ?? [] as $slide) {
        $photo = (string) ($slide['photo'] ?? '');
        $video = (string) ($slide['video'] ?? '');

        $url = $photo !== ''
            ? (Images::existingUrl($photo, 'hero') ?? Images::existingUrl($photo))
            : (Videos::existingUrl($video, 'hero') ?? Videos::existingUrl($video));

        $poster = $url && $video !== ''
            ? Images::existingUrl((string) ($slide['poster'] ?? ''), 'hero')
            : null;

        $slides[] = [
            'url' => $url,
            'type' => $url === null ? null : ($photo !== '' ? 'photo' : 'video'),
            'poster' => $poster,
            // A floor keeps a mistyped duration from freezing the sequence.
            'duration' => max(400, (int) ($slide['duration'] ?? 1000)),
            'caption' => (bool) ($slide['caption'] ?? false),
        ];
    }

    // Dots and rotation only mean anything once there is more than one slide.
    $rotates = count($slides) > 1;
    $captionSlide = array_search(true, array_column($slides, 'caption'), true);
@endphp

{{--
    The banner is a sequence of full-bleed slides that rotate on their own; see
    config('school.hero.slides'). The first slide is marked active in the markup
    so the banner is complete without JavaScript, and <x-hero-slider> in
    resources/js/app.js takes over the timing once it loads.
--}}
<section
    @if ($rotates)
        data-hero-slider
    @endif
    class="relative isolate flex min-h-[28rem] items-center overflow-hidden bg-brick-green lg:min-h-[34rem]"
>
    <div aria-hidden="true" class="absolute inset-0 -z-20">
        @foreach ($slides as $index => $slide)
            <div
                class="hero-slide absolute inset-0"
                data-hero-slide="{{ $index }}"
                data-hero-duration="{{ $slide['duration'] }}"
                data-hero-text="{{ $slide['caption'] ? 'true' : 'false' }}"
                data-state="{{ $index === 0 ? 'active' : '' }}"
            >
                @if ($slide['url'])
                    @if ($slide['type'] === 'video')
                        {{-- A clip: autoplay is only honoured when muted and inline. --}}
                        <video
                            @if ($slide['poster']) poster="{{ $slide['poster'] }}" @endif
                            class="size-full object-cover object-center"
                            autoplay
                            muted
                            loop
                            playsinline
                            preload="metadata"
                            tabindex="-1"
                        >
                            <source src="{{ $slide['url'] }}" />
                        </video>
                    @else
                        <img
                            src="{{ $slide['url'] }}"
                            alt=""
                            class="size-full object-cover object-center"
                        />
                    @endif
                @else
                    <div class="size-full bg-gradient-to-br from-school-700 via-school-800 to-school-950"></div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- The direction lives in the config alongside the stops, so the shade can
         follow the caption wherever it sits. --}}
    <div
        aria-hidden="true"
        class="absolute inset-0 -z-10 {{ $hero['overlay'] }}"
    ></div>

    <div aria-hidden="true" class="absolute -top-32 right-0 -z-10 size-[26rem] rounded-full bg-gold-400/10 blur-3xl"></div>
    <div aria-hidden="true" class="absolute -bottom-40 -left-32 -z-10 size-[24rem] rounded-full bg-school-400/10 blur-3xl"></div>

    {{-- The caption sits above the rotating photos instead of inside a slide,
         so it rises into place rather than travelling sideways with them. It
         only appears while its own slide is on screen. --}}
    @if ($captionSlide !== false)
        <div class="mx-auto w-full max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
            <div
                class="hero-caption mx-auto max-w-3xl text-center"
                data-hero-caption
                data-state="{{ $captionSlide === 0 ? 'active' : '' }}"
            >
                <h1 class="text-3xl leading-tight font-extrabold tracking-tight text-white sm:text-4xl lg:text-5xl">
                    {{ config('school.name') }}
                </h1>

                <p class="mt-4 text-base leading-relaxed text-school-100/90 sm:text-lg">
                    {{ config('school.tagline') }}
                </p>

                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    <a
                        href="{{ route('spmb') }}"
                        class="group inline-flex items-center gap-2 rounded-full bg-transparent px-6 py-3 text-sm font-semibold text-white ring-1 ring-gold-400/80 transition hover:bg-gold-500/20 ring-school-focus"
                    >
                        Daftar Sekarang
                        <x-icon name="arrow-right" class="size-4 transition group-hover:translate-x-0.5" />
                    </a>

                    <a
                        href="{{ config('school.contact.whatsapp_link') }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="group inline-flex items-center gap-2 rounded-full bg-transparent px-6 py-3 text-sm font-semibold text-white ring-1 ring-white/40 transition hover:bg-white/10 ring-school-focus"
                    >
                        <x-icon name="whatsapp" class="size-4 text-white/80 transition group-hover:text-white" />
                        Konsultasi
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Manual control. The sequence keeps rotating on its own, so these are a
         shortcut rather than a requirement. --}}
    @if ($rotates)
        <div class="absolute right-4 bottom-5 z-10 flex items-center gap-2 sm:right-6 lg:right-8">
            @foreach ($slides as $index => $slide)
                <button
                    type="button"
                    data-hero-dot="{{ $index }}"
                    aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                    aria-label="Tampilkan foto {{ $index + 1 }}"
                    class="h-2 w-2 rounded-full bg-white/40 transition-all duration-300 hover:bg-white/70 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gold-400 data-[current=true]:w-7 data-[current=true]:bg-white"
                ></button>
            @endforeach
        </div>
    @endif
</section>