@php
    // Heading copy comes from config('school.programs'); the panel list itself
    // is not duplicated — the section keeps only the catalogue entries whose
    // slug is in config('school.programs.slugs'), the six programs the school
    // offers (Kuliner, DKV, TKJ, AK, MP, BD). array_intersect_key preserves the
    // catalogue order, so the panels open in slug order. The accent gradient
    // per programme lives here instead of in config: Tailwind only compiles
    // classes it can see inside its scanned sources (resources/), and PHP
    // config files are not scanned.
    $programs = config('school.programs');
    $catalog = array_intersect_key(
        config('school.program_catalog.items', []),
        array_flip($programs['slugs'])
    );

    $accents = [
        'Kuliner' => 'from-rose-600 via-rose-700 to-rose-950',
        'DKV' => 'from-orange-600 via-orange-700 to-orange-950',
        'TKJ' => 'from-blue-600 via-blue-700 to-blue-950',
        'AK' => 'from-amber-500 via-amber-600 to-amber-950',
        'MP' => 'from-purple-600 via-purple-700 to-purple-950',
        'BD' => 'from-emerald-600 via-emerald-700 to-emerald-950',
    ];
@endphp

<section id="program-keahlian" class="bg-slate-50 py-20 lg:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        {{-- The heading and the standfirst travel in from opposite edges, so the
             two halves cross as the reader arrives. --}}
        <div class="grid gap-8 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-7" data-reveal data-reveal-from="left">
                <p class="flex items-center gap-3 text-xs font-semibold tracking-[0.18em] text-school-700 uppercase">
                    <span aria-hidden="true" class="h-px w-10 bg-gold-500"></span>
                    {{ $programs['eyebrow'] }}
                </p>

                <h2 class="mt-5 text-2xl leading-snug font-bold tracking-tight text-balance text-slate-900 sm:text-3xl">
                    {{ $programs['title'] }}
                </h2>
            </div>

            <p class="text-base leading-relaxed text-slate-600 lg:col-span-5" data-reveal data-reveal-from="right">
                {{ $programs['intro'] }}
            </p>
        </div>

        {{-- Hover accordion, zero JS: the six anchors share one flex row on
             desktop, the first panel open and the rest slim vertical tabs. A
             hover (or keyboard focus) grows a panel to fill most of the row
             while the others shrink back, with the two content states
             crossfading - see the .programs-* rules in app.css. Below lg the
             same anchors simply stack as full-width cards. --}}
        <div
            class="programs-accordion mt-12 grid grid-cols-1 gap-5 min-[400px]:grid-cols-2 lg:flex lg:h-[34rem] lg:flex-row lg:gap-0 lg:overflow-hidden lg:rounded-3xl lg:shadow-2xl lg:shadow-school-900/10"
            data-reveal-group
            data-reveal-slow
        >
            @foreach ($catalog as $slug => $program)
                @php
                    $accent = $accents[$program['name']] ?? 'from-school-700 via-school-800 to-school-950';
                @endphp
                <a
                    href="{{ route('program.'.$slug) }}"
                    class="programs-panel group relative flex flex-col overflow-hidden rounded-3xl bg-gradient-to-br p-6 text-white ring-1 ring-white/10 transition-shadow duration-300 hover:shadow-2xl hover:shadow-school-900/20 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gold-400 lg:justify-end lg:rounded-none lg:p-8 {{ $accent }}"
                >
                    {{-- The collapsed state's face: the programme name in
                         vertical type beside a plus sign. Hidden on mobile, faded
                         out whenever the panel is the open one. --}}
                    <span class="programs-panel-tab" aria-hidden="true">
                        <span class="programs-panel-tab-name">{{ $program['name'] }}</span>
                        <span class="flex size-10 items-center justify-center rounded-full bg-white/15 ring-1 ring-white/40">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" class="size-5" aria-hidden="true">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                        </span>
                    </span>

                    <span
                        aria-hidden="true"
                        class="pointer-events-none absolute -top-24 -right-24 hidden size-72 rounded-full bg-white/10 blur-3xl lg:block"
                    ></span>

                    <span
                        data-program-icon
                        class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/25 transition-transform duration-300 group-hover:scale-105"
                    >
                        <x-icon :name="$program['icon']" class="size-6" />
                    </span>

                    <h3 class="mt-5 text-2xl leading-tight font-extrabold tracking-tight max-lg:text-xl lg:mt-8">
                        {{ $program['name'] }}
                    </h3>

                    <p class="mt-3 max-w-xl text-sm leading-relaxed text-white/85 max-lg:line-clamp-3 lg:mt-auto lg:text-base">
                        {{ $program['description'] }}
                    </p>

                    <span class="inline-flex items-center gap-2 text-sm font-bold tracking-wide uppercase max-lg:mt-auto max-lg:pt-7 lg:mt-7">
                        Pelajari {{ $program['name'] }}
                        <x-icon name="arrow-right" class="size-4 transition-transform duration-300 group-hover:translate-x-1" />
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>