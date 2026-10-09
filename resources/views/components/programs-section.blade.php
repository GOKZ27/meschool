@php
    // Every program is an entry in config('school.programs.items'), so the
    // section is copy-driven and a program is added in one place.
    $programs = config('school.programs');
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

        {{-- data-reveal-slow is the long, wide-spaced cascade: the cards take
             their time travelling up, so the row assembles instead of snapping in. --}}
        <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4" data-reveal-group data-reveal-slow>
            @foreach ($programs['items'] as $program)
                <a
                    href="{{ route($program['route']) }}"
                    class="group relative flex flex-col rounded-2xl bg-white p-6 ring-1 ring-slate-200 transition duration-300 hover:-translate-y-1 hover:ring-school-300 hover:shadow-lg hover:shadow-school-900/5 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-school-500"
                >
                    {{-- A hairline that draws itself across the top edge on hover. --}}
                    <span
                        aria-hidden="true"
                        class="absolute inset-x-6 top-0 h-px origin-left scale-x-0 bg-gold-500 transition-transform duration-500 group-hover:scale-x-100"
                    ></span>

                    <span
                        data-program-icon
                        class="flex size-12 items-center justify-center rounded-xl bg-school-50 text-school-700 ring-1 ring-school-100 transition duration-300 group-hover:bg-school-700 group-hover:text-white group-hover:ring-school-700"
                    >
                        <x-icon :name="$program['icon']" class="size-6" />
                    </span>

                    <h3 class="mt-5 text-lg font-bold tracking-tight text-slate-900">{{ $program['name'] }}</h3>

                    <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $program['description'] }}</p>

                    <span class="mt-6 inline-flex items-center gap-1.5 text-sm font-semibold text-school-700">
                        Pelajari {{ $program['name'] }}
                        <x-icon name="arrow-right" class="size-4 transition-transform duration-300 group-hover:translate-x-1" />
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>