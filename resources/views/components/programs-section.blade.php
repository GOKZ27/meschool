@php
    $programs = config('school.programs');
    $catalog = array_intersect_key(
        config('school.program_catalog.items', []),
        array_flip($programs['slugs'])
    );

    $programImage = \App\Support\Images::existingUrl('hero1.jpeg', 'hero');
@endphp

<section id="program-keahlian" class="pk-section">
    <div class="pk-shell">
        <header class="pk-heading">
            <div class="pk-heading__title" data-reveal data-reveal-from="left">
                <p class="pk-eyebrow">
                    <span aria-hidden="true"></span>
                    {{ $programs['eyebrow'] }}
                </p>
                <h2>{{ $programs['title'] }}</h2>
            </div>

            <p class="pk-heading__intro" data-reveal data-reveal-from="right">
                {{ $programs['intro'] }}
            </p>
        </header>

        <div class="pk-grid" data-reveal-group data-reveal-slow>
            @foreach ($catalog as $slug => $program)
                <a
                    href="{{ route('program.'.$slug) }}"
                    class="pk-card pk-card--{{ $slug }}"
                    data-pk-card
                >
                    <article class="pk-card__article">
                        <span class="pk-card__art" aria-hidden="true">
                            <img
                                src="{{ $programImage }}"
                                alt=""
                                width="960"
                                height="720"
                                loading="lazy"
                                decoding="async"
                            >
                        </span>

                        <span class="pk-card__copy">
                            <span class="pk-card__eyebrow">Program Keahlian</span>
                            <h3>{{ $program['name'] }}</h3>
                            <span class="pk-card__rule" aria-hidden="true"></span>
                            <p>{{ $program['description'] }}</p>

                            <span class="pk-card__cta">
                                <span>Pelajari {{ $program['name'] }}</span>
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 12h15m-6-6 6 6-6 6" />
                                </svg>
                            </span>
                        </span>

                        @if (in_array($slug, ['kuliner', 'dkv', 'tkj'], true))
                            <span class="pk-card__motif" data-program-icon aria-hidden="true">
                                @if ($slug === 'kuliner')
                                    <svg viewBox="0 0 160 160" fill="none">
                                        <path d="M47 82c-14 0-24-10-24-23 0-12 9-22 21-23 4-17 19-28 37-28 17 0 31 10 36 25 15 1 27 12 27 27 0 13-10 22-25 22H47Z" />
                                        <path d="m47 82 7 48h51l8-48M59 130l-2 12h46l-2-12M35 104l34 34M40 94l42 42M29 108l7-7M77 143l32-39M81 148l37-45M109 104l10 8M59 126l-8 8M68 117l-8 8" />
                                    </svg>
                                @elseif ($slug === 'dkv')
                                    <svg viewBox="0 0 160 160" fill="none">
                                        <path d="M77 20c-33 0-58 24-58 56 0 31 24 54 55 54h9c8 0 13-8 9-15l-5-9c-3-6 1-13 8-13h13c18 0 32-14 32-32 0-23-27-41-63-41Z" />
                                        <circle cx="51" cy="57" r="8" />
                                        <circle cx="77" cy="43" r="8" />
                                        <circle cx="103" cy="53" r="8" />
                                        <circle cx="51" cy="84" r="8" />
                                        <path d="m112 119 29-71 11 4-29 71-18 13 7-17Z" />
                                    </svg>
                                @else
                                    <svg viewBox="0 0 160 160" fill="none">
                                        <rect x="22" y="17" width="116" height="76" rx="5" />
                                        <path d="M14 106h132l-9 11H23l-9-11ZM80 94v12M57 48l-13 11 13 11M103 48l13 11-13 11M88 45l-10 28" />
                                        <circle cx="111" cy="122" r="25" />
                                        <path d="M86 122h50M111 97c7 7 11 15 11 25s-4 18-11 25c-7-7-11-15-11-25s4-18 11-25Z" />
                                        <path d="m111 97-9 8m18 0-18 34m18 0-18-9" />
                                    </svg>
                                @endif
                            </span>
                        @else
                            <span class="sr-only" data-program-icon aria-hidden="true">
                                <x-icon :name="$program['icon']" class="size-6" />
                            </span>
                        @endif
                    </article>
                </a>
            @endforeach
        </div>
    </div>
</section>
