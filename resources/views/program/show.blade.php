<x-layouts.app
    :title="$page['name'].' — '.config('school.name')"
    :description="$page['description'] ?? $page['tagline'] ?? null"
>
    <x-header-top />
    <x-navbar />

    <main>
        @php
            $eyebrow = config('school.program_catalog.eyebrow', 'Program Keahlian');
            $all = config('school.program_catalog.items', []);
            $competencyCount = count($page['competencies'] ?? []);
            $careerCount = count($page['careers'] ?? []);
        @endphp

        {{-- Hero band: the other programs as pills underneath so a section page
             never dead-ends, with the program's own icon as a faint watermark. --}}
        <section class="relative isolate overflow-hidden bg-brick-green">
            <div aria-hidden="true" class="pointer-events-none absolute -top-32 -right-32 size-96 rounded-full bg-white/5 blur-3xl"></div>

            @if (isset($page['icon']))
                <x-icon :name="$page['icon']" class="pointer-events-none absolute -right-14 bottom-0 size-64 rotate-12 text-white/5 sm:size-80" />
            @endif

            <div class="relative mx-auto max-w-7xl px-4 py-14 sm:px-6 sm:py-16 lg:px-8">
                <div data-reveal-group>
                    <nav aria-label="Jejak halaman" class="flex flex-wrap items-center gap-1.5 text-xs font-medium text-school-100/80">
                        <a href="{{ route('home') }}" class="transition hover:text-white">Beranda</a>
                        <span aria-hidden="true">&middot;</span>
                        <span>{{ $eyebrow }}</span>
                        <span aria-hidden="true">&middot;</span>
                        <span class="font-semibold text-gold-400" aria-current="page">{{ $page['name'] }}</span>
                    </nav>

                    <div class="mt-6 flex flex-wrap items-center gap-5 lg:gap-7">
                        <span class="relative inline-flex size-16 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white text-school-700 shadow-lg ring-1 ring-white/30">
                            <span aria-hidden="true" class="absolute inset-0 bg-gradient-to-tr from-gold-400/0 to-gold-400/25"></span>
                            <x-icon :name="$page['icon']" class="relative size-8" />
                        </span>

                        <div>
                            <h1 class="text-3xl leading-snug font-bold tracking-tight text-balance text-white sm:text-4xl">
                                {{ $page['name'] }}
                            </h1>
                            <p class="mt-2 max-w-2xl text-sm leading-relaxed text-school-100/85 sm:text-base">
                                {{ $page['tagline'] }}
                            </p>
                        </div>
                    </div>

                    @if (count($all) > 1)
                        <div class="mt-9 flex flex-wrap items-center gap-2.5" data-program-pills>
                            @foreach ($all as $key => $entry)
                                <a
                                    href="{{ route('program.'.$key) }}"
                                    @class([
                                        'inline-flex items-center gap-2 rounded-full bg-gold-500 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-gold-500/25 transition-all hover:-translate-y-0.5 hover:bg-gold-600 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gold-400' => $key === $slug,
                                        'inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/20 transition-all hover:-translate-y-0.5 hover:bg-white/20 group focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gold-400' => $key !== $slug,
                                    ])
                                    @if ($key === $slug) aria-current="page" @endif
                                >
                                    @if ($key === $slug)
                                        <x-icon name="check-badge" class="size-4 shrink-0" />
                                    @else
                                        <x-icon name="arrow-right" class="size-4 shrink-0 -rotate-45 transition-transform duration-300 group-hover:translate-x-0.5" />
                                    @endif
                                    {{ $entry['name'] }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- Intro: a dark profile card carrying the program's shape sinks into
             the white field while the description travels in from the other
             side, so the two halves cross as the reader scrolls. --}}
        <section class="bg-white py-20 lg:py-24">
            <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 sm:px-6 lg:grid-cols-12 lg:gap-14 lg:px-8">
                <div class="lg:col-span-5" data-reveal data-reveal-from="left">
                    <div class="relative isolate overflow-hidden rounded-3xl bg-school-950 p-8 text-white shadow-xl ring-1 ring-school-900">
                        @if (isset($page['icon']))
                            <x-icon :name="$page['icon']" class="pointer-events-none absolute -right-8 -bottom-8 size-40 text-white/5" />
                        @endif

                        <span class="relative flex size-14 items-center justify-center rounded-2xl bg-gold-500 text-school-950 shadow-lg shadow-gold-500/30">
                            <x-icon :name="$page['icon']" class="size-7" />
                        </span>

                        <p class="relative mt-6 flex items-center gap-3 text-xs font-semibold tracking-[0.18em] text-gold-400 uppercase">
                            <span aria-hidden="true" class="h-px w-8 bg-gold-500"></span>
                            {{ $eyebrow }}
                        </p>

                        <h2 class="relative mt-3 text-2xl font-bold tracking-tight">
                            {{ $page['name'] }}
                        </h2>

                        <dl class="relative mt-6 grid grid-cols-2 gap-3">
                            <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
                                <dt class="text-[0.65rem] font-semibold tracking-wider text-school-100/70 uppercase">Kompetensi</dt>
                                <dd class="mt-1 text-2xl font-bold text-gold-400">{{ $competencyCount }}</dd>
                            </div>
                            <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
                                <dt class="text-[0.65rem] font-semibold tracking-wider text-school-100/70 uppercase">Peluang Karier</dt>
                                <dd class="mt-1 text-2xl font-bold text-gold-400">{{ $careerCount }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="lg:col-span-7" data-reveal data-reveal-from="right">
                    <p class="flex items-center gap-3 text-xs font-semibold tracking-[0.18em] text-school-700 uppercase">
                        <span aria-hidden="true" class="h-px w-10 bg-gold-500"></span>
                        Tentang Program
                    </p>

                    <p class="mt-5 text-lg leading-relaxed font-medium text-slate-800 lg:text-xl">
                        {{ $page['description'] }}
                    </p>

                    <span class="mt-7 inline-flex items-center gap-2 rounded-full bg-school-50 px-4 py-2 text-xs font-semibold text-school-700 ring-1 ring-school-100">
                        <x-icon name="graduation-cap" class="size-4 text-gold-600" />
                        {{ $page['tagline'] }}
                    </span>
                </div>
            </div>
        </section>

        {{-- Competencies and careers as twin cards on slate, so the two lists
             read as one block rather than two competing sections. --}}
        <section class="bg-slate-50 py-20 lg:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-6 lg:grid-cols-2">
                    <article class="group relative overflow-hidden rounded-3xl bg-white p-8 ring-1 ring-slate-200 transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:shadow-slate-900/5 hover:ring-school-300" data-reveal data-reveal-from="left">
                        <div aria-hidden="true" class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-gold-500 to-gold-400"></div>

                        <div class="flex items-center gap-3">
                            <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-gold-500/15 text-gold-600 transition-transform duration-300 group-hover:scale-110">
                                <x-icon name="check-badge" class="size-5" />
                            </span>
                            <h2 class="text-lg font-bold tracking-tight text-slate-900">Kompetensi yang Dipelajari</h2>
                            <span class="ml-auto hidden rounded-full bg-school-50 px-3 py-1 text-xs font-bold text-school-700 ring-1 ring-school-100 sm:inline-flex">{{ $competencyCount }}</span>
                        </div>

                        <div class="mt-4 h-px bg-slate-100"></div>

                        <ul class="mt-6 space-y-3.5">
                            @foreach ($page['competencies'] as $competency)
                                <li class="flex items-start gap-3 transition-transform duration-200 hover:translate-x-1">
                                    <span class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full bg-gold-500/10 text-gold-600">
                                        <x-icon name="check-badge" class="size-4" />
                                    </span>
                                    <span class="text-sm leading-relaxed text-slate-700">{{ $competency }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </article>

                    <article class="group relative overflow-hidden rounded-3xl bg-white p-8 ring-1 ring-slate-200 transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:shadow-slate-900/5 hover:ring-school-300" data-reveal data-reveal-from="right">
                        <div aria-hidden="true" class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-school-700 to-school-800"></div>

                        <div class="flex items-center gap-3">
                            <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-school-700/10 text-school-700 transition-transform duration-300 group-hover:scale-110">
                                <x-icon name="briefcase" class="size-5" />
                            </span>
                            <h2 class="text-lg font-bold tracking-tight text-slate-900">Peluang Karier</h2>
                            <span class="ml-auto hidden rounded-full bg-school-50 px-3 py-1 text-xs font-bold text-school-700 ring-1 ring-school-100 sm:inline-flex">{{ $careerCount }}</span>
                        </div>

                        <div class="mt-4 h-px bg-slate-100"></div>

                        <ul class="mt-6 space-y-3.5">
                            @foreach ($page['careers'] as $career)
                                <li class="flex items-start gap-3 transition-transform duration-200 hover:translate-x-1">
                                    <span class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full bg-school-700/10 text-school-700">
                                        <x-icon name="chevron-right" class="size-4" />
                                    </span>
                                    <span class="text-sm leading-relaxed text-slate-700">{{ $career }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </article>
                </div>
            </div>
        </section>

        {{-- Closing call to action: the next step after deciding on a program. --}}
        <section class="relative isolate overflow-hidden bg-school-950 py-16 lg:py-20" data-program-cta>
            <div aria-hidden="true" class="pointer-events-none absolute top-0 left-1/2 size-[36rem] -translate-x-1/2 -translate-y-1/2 rounded-full bg-gold-500/10 blur-3xl"></div>

            <div class="relative mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8" data-reveal>
                @if (isset($page['icon']))
                    <span class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-gold-500/15 ring-1 ring-gold-500/25">
                        <x-icon :name="$page['icon']" class="size-7 text-gold-400" />
                    </span>
                @endif

                <h2 class="mt-6 text-2xl leading-snug font-bold tracking-tight text-balance text-white sm:text-3xl">
                    Tertarik masuk {{ $page['name'] }}?
                </h2>

                <p class="mx-auto mt-3 max-w-2xl text-sm leading-relaxed text-school-100/85 sm:text-base">
                    Konsultasikan pilihan jurusan Anda dengan tim kami, atau lihat lowongan kerja
                    yang terbuka untuk alumni SMK Islamiyah Ciputat.
                </p>

                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    <a
                        href="{{ route('kontak') }}"
                        class="group inline-flex items-center gap-2 rounded-full bg-gold-500 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-gold-500/25 transition-all hover:-translate-y-0.5 hover:bg-gold-600 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gold-400"
                    >
                        <x-icon name="phone" class="size-4" />
                        Hubungi Sekolah
                        <x-icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-0.5" />
                    </a>

                    <a
                        href="{{ route('bkk') }}"
                        class="inline-flex items-center gap-2 rounded-full bg-white/10 px-6 py-2.5 text-sm font-semibold text-white ring-1 ring-white/25 transition-all hover:-translate-y-0.5 hover:bg-white/20 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-white"
                    >
                        <x-icon name="briefcase" class="size-4" />
                        Jelajahi Lowongan Kerja
                    </a>
                </div>
            </div>
        </section>

        <x-contact-strip />
    </main>
</x-layouts.app>