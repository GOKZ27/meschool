<x-layouts.app :title="'Berita — ' . config('school.name')">
    <x-header-top />
    <x-navbar />

    <main>
        @php
            $copy = config('school.news.archive');
            $isFiltered = ($filters['category'] ?? null) !== null || ($filters['year'] ?? null) !== null || $filters['q'] !== '';
        @endphp

        {{-- The archive opens with a brick-green band: the one place on this
             page where the brand colour is the background rather than the
             accent, so the search bar has somewhere loud to sit. --}}
        <section class="bg-brick-green">
            <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 sm:py-16 lg:px-8">
                <p class="flex items-center gap-3 text-xs font-semibold tracking-[0.18em] text-gold-400 uppercase">
                    <span aria-hidden="true" class="h-px w-10 bg-gold-400"></span>
                    {{ $copy['eyebrow'] }}
                </p>

                <h1 class="mt-4 text-2xl leading-snug font-bold tracking-tight text-balance text-white sm:text-3xl">
                    {{ $copy['title'] }}
                </h1>

                <p class="mt-3 max-w-2xl text-sm leading-relaxed text-school-100/85 sm:text-base">
                    {{ $copy['intro'] }}
                </p>

                {{-- One GET form carries every filter, so the browser, the
                     back button and a shared link all agree on what is being
                     shown. `page` is deliberately absent: a new query starts
                     again from page one. --}}
                <form method="GET" action="{{ route('berita') }}" class="mt-8 flex max-w-2xl gap-2">
                    @if ($filters['category'] !== null)
                        <input type="hidden" name="kategori" value="{{ $filters['category'] }}">
                    @endif

                    @if ($filters['year'] !== null)
                        <input type="hidden" name="tahun" value="{{ $filters['year'] }}">
                    @endif

                    <label for="berita-q" class="sr-only">{{ $copy['search_label'] }}</label>

                    <div class="relative flex-1">
                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-school-100/70">
                            <x-icon name="search" class="size-5" />
                        </span>

                        <input
                            id="berita-q"
                            type="search"
                            name="q"
                            value="{{ $filters['q'] }}"
                            placeholder="{{ $copy['search_placeholder'] }}"
                            class="h-12 w-full rounded-full border-0 bg-white/10 pl-12 pr-4 text-sm text-white placeholder:text-school-100/70 ring-1 ring-white/20 transition focus:bg-white/15 focus:ring-gold-400 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gold-400"
                        >
                    </div>

                    <button
                        type="submit"
                        class="inline-flex h-12 shrink-0 items-center gap-2 rounded-full bg-gold-500 px-5 text-sm font-semibold text-white transition hover:bg-gold-600 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-white"
                    >
                        <x-icon name="search" class="size-4" />
                        <span class="hidden sm:inline">{{ $copy['search_label'] }}</span>
                    </button>
                </form>
            </div>
        </section>

        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-12 lg:gap-12 lg:px-8">
            {{-- ── The results ─────────────────────────────────────────── --}}
            <div class="lg:col-span-8">
                <div class="flex flex-wrap items-baseline justify-between gap-4 border-b-2 border-slate-900 pb-3">
                    <p class="text-sm text-slate-600">
                        <span class="font-semibold text-slate-900">{{ $news->total() }}</span>
                        berita ditemukan
                        @if ($filters['q'] !== '')
                            untuk &ldquo;<span class="font-semibold text-slate-900">{{ $filters['q'] }}</span>&rdquo;
                        @endif
                    </p>

                    @if ($isFiltered)
                        <a href="{{ route('berita') }}" class="text-xs font-semibold text-school-700 hover:text-school-800">
                            {{ $copy['reset'] }}
                        </a>
                    @endif
                </div>

                @if ($news->isEmpty())
                    <div class="mt-8 rounded-2xl bg-slate-50 p-10 text-center ring-1 ring-slate-200">
                        <x-icon name="search" class="size-8 text-slate-400" />
                        <p class="mt-4 text-sm leading-relaxed text-slate-600">{{ $copy['empty'] }}</p>

                        <a
                            href="{{ route('berita') }}"
                            class="mt-6 inline-flex items-center gap-2 rounded-full bg-school-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-school-800"
                        >
                            {{ $copy['reset'] }}
                            <x-icon name="arrow-right" class="size-4" />
                        </a>
                    </div>
                @else
                    <ul class="mt-6 grid grid-cols-1 gap-5 min-[400px]:grid-cols-2" data-reveal-group>
                        @foreach ($news as $article)
                            <li>
                                <x-news.card :article="$article" />
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-10">
                        {{ $news->links() }}
                    </div>
                @endif
            </div>

            {{-- ── The filters, as a list rather than a second grid ────── --}}
            <aside class="space-y-8 lg:col-span-4">
                <div data-reveal data-reveal-from="right">
                    <h2 class="border-b-2 border-slate-900 pb-3 text-base font-bold tracking-tight text-slate-900">
                        {{ $copy['category_label'] }}
                    </h2>

                    <ul class="mt-4 space-y-1">
                        <li>
                            <a
                                href="{{ route('berita') }}"
                                @class([
                                    'flex items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
                                    'bg-school-700 text-white' => $filters['category'] === null,
                                    'text-slate-700 hover:bg-slate-100' => $filters['category'] !== null,
                                ])
                            >
                                <span class="inline-flex items-center gap-2.5">
                                    <x-icon name="document" class="size-4" />
                                    Semua kategori
                                </span>
                                <span class="text-xs tabular-nums opacity-70">{{ $news->total() }}</span>
                            </a>
                        </li>

                        @foreach ($categories as $info)
                            <li>
                                <a
                                    href="{{ route('berita', array_filter(['kategori' => $info['slug'], 'tahun' => $filters['year']])) }}"
                                    @class([
                                        'flex items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
                                        'bg-school-700 text-white' => $filters['category'] === $info['slug'],
                                        'text-slate-700 hover:bg-slate-100' => $filters['category'] !== $info['slug'],
                                    ])
                                    @if ($filters['category'] === $info['slug']) aria-current="page" @endif
                                >
                                    <span class="inline-flex min-w-0 items-center gap-2.5">
                                        <x-icon :name="$info['icon']" class="size-4 shrink-0 text-gold-600" />
                                        <span class="truncate">{{ $info['label'] }}</span>
                                    </span>
                                    <span class="text-xs tabular-nums opacity-70">{{ $info['count'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                @if ($years->isNotEmpty())
                    <div data-reveal data-reveal-from="right">
                        <h2 class="border-b-2 border-slate-900 pb-3 text-base font-bold tracking-tight text-slate-900">
                            {{ $copy['year_label'] }}
                        </h2>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <a
                                href="{{ route('berita', array_filter(['kategori' => $filters['category']])) }}"
                                @class([
                                    'rounded-full px-4 py-2 text-sm font-semibold transition',
                                    'bg-slate-900 text-white' => $filters['year'] === null,
                                    'bg-white text-slate-700 ring-1 ring-slate-300 hover:bg-slate-100' => $filters['year'] !== null,
                                ])
                            >
                                {{ $copy['all_years'] }}
                            </a>

                            @foreach ($years as $year)
                                <a
                                    href="{{ route('berita', array_filter(['kategori' => $filters['category'], 'tahun' => $year])) }}"
                                    @class([
                                        'rounded-full px-4 py-2 text-sm font-semibold transition',
                                        'bg-slate-900 text-white' => $filters['year'] === (string) $year,
                                        'bg-white text-slate-700 ring-1 ring-slate-300 hover:bg-slate-100' => $filters['year'] !== (string) $year,
                                    ])
                                    @if ($filters['year'] === (string) $year) aria-current="page" @endif
                                >
                                    {{ $year }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($popular->isNotEmpty())
                    <div data-reveal data-reveal-from="right">
                        <h2 class="flex items-baseline justify-between gap-4 border-b-2 border-slate-900 pb-3 text-base font-bold tracking-tight text-slate-900">
                            Trending
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-school-700 uppercase">
                                <x-icon name="eye" class="size-4" />
                                Paling banyak dibaca
                            </span>
                        </h2>

                        <ol class="mt-2">
                            @foreach ($popular as $index => $article)
                                <x-news.rank :article="$article" :rank="$index + 1" :views="true" />
                            @endforeach
                        </ol>
                    </div>
                @endif
            </aside>
        </div>

        <x-contact-strip />
    </main>
</x-layouts.app>
