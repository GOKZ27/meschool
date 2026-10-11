@props(['news'])

@php
    $copy = config('school.news.home');
    $featured = $news['featured'];
    $latest = $news['latest'];
    $grid = $news['grid'];
    $past = $news['past'];
    $trending = $news['trending'];
    $categories = $news['categories'];
    $galleryPanels = $copy['gallery_panels'] ?? [];
    $hasStories = $featured !== null
        || $latest->isNotEmpty()
        || $grid->isNotEmpty()
        || $past->isNotEmpty()
        || $trending->isNotEmpty();
@endphp

{{-- The home page is a preview of the whole news feature. The heading runs
     centred, then the stories read in five very different shapes — a lead
     story with two spotlight cards tucked under it beside a hairline list,
     the dated archive cards, a ranked trending list and the photo wall — so
     the section feels like a front page rather than one card component
     repeated. Every block is skipped when its own query came back empty, so
     a fresh install with no articles still renders. --}}
<section id="berita" class="bg-slate-50 py-20 lg:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- Masthead: the eyebrow sits between two gold rules and the heading
             runs centred, the quietest possible way to open the section. --}}
        <div class="mx-auto max-w-2xl text-center" data-reveal>
            <p class="inline-flex items-center gap-3 text-xs font-semibold tracking-[0.18em] text-school-700 uppercase">
                <span aria-hidden="true" class="h-px w-8 bg-gold-500"></span>
                {{ $copy['eyebrow'] }}
                <span aria-hidden="true" class="h-px w-8 bg-gold-500"></span>
            </p>

            <h2 class="mt-5 text-3xl leading-none font-bold tracking-tight text-balance text-slate-900 sm:text-4xl">
                {{ $copy['title'] }}
            </h2>

            <p class="mt-4 text-base leading-relaxed text-slate-600">{{ $copy['intro'] }}</p>
        </div>

        @if (! $hasStories)
            {{-- Nothing published yet: say so instead of showing empty boxes. --}}
            <div class="mt-12 rounded-2xl bg-white p-10 text-center ring-1 ring-slate-200" data-reveal>
                <x-icon name="document" class="size-8 text-school-600" />
                <p class="mt-4 text-sm leading-relaxed text-slate-600">
                    Belum ada berita yang diterbitkan. Kabar terbaru sekolah akan muncul di sini.
                </p>
            </div>
        @else
            {{-- ── 1. Pengumuman + berita terbaru ────────────────────────
                 A two-column front page: the left holds the "Pengumuman"
                 panel — one lead story with the largest picture on the page,
                 and two spotlight cards tucked under it — while the right is
                 a hairline list, deliberately image-free, so the columns
                 never compete for the same attention. A row of category pills
                 above the list links straight into the filtered archive; the
                 active-story caption rides the featured label. --}}
            <div class="mt-12 grid gap-10 lg:grid-cols-12">
                <div class="lg:col-span-7">
                <article data-reveal data-reveal-from="left">
                    <a
                        href="{{ route('berita.show', $featured->slug) }}"
                        class="group block overflow-hidden rounded-3xl bg-white ring-1 ring-slate-200 transition duration-300 hover:ring-school-300 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-school-500"
                    >
                        <x-news.thumb
                            :url="$featured->imageUrl()"
                            :alt="$featured->title"
                            :icon="$featured->categoryInfo()['icon']"
                            aspect="aspect-[16/9] sm:aspect-[2/1]"
                            class="rounded-t-3xl"
                        />

                        <div class="p-6 sm:p-8">
                            <div class="flex flex-wrap items-center gap-3">
                                <x-news.chip :category="$featured->category" />
                                <span class="text-xs font-semibold tracking-wide text-gold-600 uppercase">
                                    {{ $copy['featured_label'] }}
                                </span>
                            </div>

                            <h3 class="mt-4 text-xl leading-snug font-bold tracking-tight text-balance text-slate-900 transition group-hover:text-school-700 sm:text-2xl">
                                {{ $featured->title }}
                            </h3>

                            @if ($featured->excerpt)
                                <p class="mt-3 leading-relaxed text-slate-600">{{ $featured->excerpt }}</p>
                            @endif

                            <x-news.meta :article="$featured" class="mt-5" />
                        </div>
                    </a>
                </article>

                {{-- Two spotlight cards ride under the lead story, still inside
                     the left column, so the column reads as one "Pengumuman"
                     panel: photo under a dark gradient carrying the category
                     headline, the story title below in black and the date with
                     a small clock. Every card lifts and deepens its shadow on
                     hover, so the row stays level. --}}
                @if ($grid->isNotEmpty())
                    <div class="mt-5 grid grid-cols-1 gap-5 min-[400px]:grid-cols-2" data-reveal data-reveal-from="left">
                        @foreach ($grid as $article)
                            <div class="h-full">
                                <a
                                    href="{{ route('berita.show', $article->slug) }}"
                                    class="group flex h-full flex-col overflow-hidden bg-white shadow-md shadow-slate-900/10 ring-1 ring-slate-200 transition duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:shadow-slate-900/20 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-school-500"
                                >
                                    <div class="relative aspect-[16/9] shrink-0">
                                        @if ($article->imageUrl())
                                            <img
                                                src="{{ $article->imageUrl() }}"
                                                alt="{{ $article->title }}"
                                                loading="lazy"
                                                class="absolute inset-0 size-full object-cover transition duration-500 group-hover:scale-[1.04]"
                                            >
                                        @else
                                            <span class="absolute inset-0 bg-brick-green" aria-hidden="true">
                                                <span class="absolute inset-0 flex items-center justify-center text-white/40">
                                                    <x-icon :name="$article->categoryInfo()['icon']" class="size-8" />
                                                </span>
                                            </span>
                                        @endif

                                        {{-- Dark gradient so the headline stays
                                             readable over any photo. --}}
                                        <span class="pointer-events-none absolute inset-0 bg-gradient-to-t from-school-950/85 via-school-950/40 to-school-950/10" aria-hidden="true"></span>

                                        {{-- Category headline, big and bold,
                                             centred on the image. --}}
                                        <span class="absolute inset-0 flex items-center justify-center p-4 text-center">
                                            <span class="max-w-full text-base leading-tight font-black tracking-wide text-white uppercase drop-shadow-md sm:text-lg">
                                                {{ $article->categoryLabel() }}
                                            </span>
                                        </span>
                                    </div>

                                    <div class="flex flex-1 flex-col p-5">
                                        <h3 class="text-sm leading-snug font-bold tracking-tight text-balance text-slate-900 transition group-hover:text-school-700 sm:text-base">
                                            {{ $article->title }}
                                        </h3>

                                        <p class="mt-auto inline-flex items-center gap-1.5 pt-4 text-xs text-slate-500">
                                            <x-icon name="clock" class="size-3.5 shrink-0 text-slate-400" />
                                            <time datetime="{{ $article->published_at?->toDateString() }}">{{ $article->dateLabel() }}</time>
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
                </div>

                @if ($latest->isNotEmpty())
                    <div class="lg:col-span-5" data-reveal data-reveal-from="right">
                        <div class="flex items-baseline justify-between gap-4 border-b-2 border-slate-900 pb-3">
                            <h3 class="text-lg font-bold tracking-tight text-slate-900">{{ $copy['latest_title'] }}</h3>
                            <a href="{{ route('berita') }}" class="text-xs font-semibold text-school-700 hover:text-school-800">
                                {{ $copy['latest_archive'] }}
                            </a>
                        </div>

                        {{-- The category pills are plain archive links, one hop
                             into the filtered archive — no JS, no reload hooks. --}}
                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @foreach ($copy['latest_tabs'] as $slug)
                                @php $tab = $categories[$slug] ?? null; @endphp
                                @if ($tab)
                                    <a
                                        href="{{ route('berita', ['kategori' => $slug]) }}"
                                        class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200 transition hover:text-school-700 hover:ring-school-300 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-school-500"
                                    >
                                        <x-icon :name="$tab['icon']" class="size-3.5" />
                                        {{ $tab['label'] }}
                                    </a>
                                @endif
                            @endforeach
                        </div>

                        <ul class="mt-2">
                            @foreach ($latest as $article)
                                <li>
                                    <a
                                        href="{{ route('berita.show', $article->slug) }}"
                                        class="group flex gap-5 border-b border-slate-200 py-5 transition focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-school-500"
                                    >
                                        <div class="min-w-0 flex-1">
                                            <x-news.chip :category="$article->category" />

                                            <h4 class="mt-2.5 text-[0.95rem] leading-snug font-semibold text-balance text-slate-900 transition group-hover:text-school-700">
                                                {{ $article->title }}
                                            </h4>

                                            <x-news.meta :article="$article" :views="false" class="mt-2.5" />
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            {{-- The latest list paginates the way the BKK archive does: a
                 centred pager of pill buttons — previous, a number per page,
                 next — sitting under the grid and above the archive cards. --}}
            @if ($latest->isNotEmpty() && $latest->hasPages())
                <nav class="mt-10 flex items-center justify-center gap-2" aria-label="Navigasi berita terbaru" data-reveal>
                    @if ($latest->onFirstPage())
                        <span class="inline-flex size-10 items-center justify-center rounded-full bg-white text-slate-300 ring-1 ring-slate-200" aria-hidden="true">
                            <x-icon name="chevron-left" class="size-5" />
                        </span>
                    @else
                        <a
                            href="{{ $latest->previousPageUrl() }}"
                            class="inline-flex size-10 items-center justify-center rounded-full bg-white text-slate-700 ring-1 ring-slate-200 transition hover:text-school-700 hover:ring-school-300 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-school-500"
                            aria-label="Halaman sebelumnya"
                        >
                            <x-icon name="chevron-left" class="size-5" />
                        </a>
                    @endif

                    @for ($page = 1; $page <= $latest->lastPage(); $page++)
                        <a
                            href="{{ $latest->url($page) }}"
                            @class([
                                'inline-flex size-10 items-center justify-center rounded-full text-sm font-semibold tabular-nums transition focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-school-500',
                                'bg-school-700 text-white shadow-sm' => $page === $latest->currentPage(),
                                'bg-white text-slate-700 ring-1 ring-slate-200 hover:text-school-700 hover:ring-school-300' => $page !== $latest->currentPage(),
                            ])
                            @if ($page === $latest->currentPage()) aria-current="page" @endif
                            aria-label="Halaman {{ $page }}"
                        >
                            {{ $page }}
                        </a>
                    @endfor

                    @if ($latest->onLastPage())
                        <span class="inline-flex size-10 items-center justify-center rounded-full bg-white text-slate-300 ring-1 ring-slate-200" aria-hidden="true">
                            <x-icon name="chevron-right" class="size-5" />
                        </span>
                    @else
                        <a
                            href="{{ $latest->nextPageUrl() }}"
                            class="inline-flex size-10 items-center justify-center rounded-full bg-white text-slate-700 ring-1 ring-slate-200 transition hover:text-school-700 hover:ring-school-300 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-school-500"
                            aria-label="Halaman berikutnya"
                        >
                            <x-icon name="chevron-right" class="size-5" />
                        </a>
                    @endif
                </nav>
            @endif

            {{-- ── 2. Masa lampau ─────────────────────────────────────────
                 A very different shape: dated archive cards. Each one leads
                 with a big day numeral and its month, reads like a filing
                 ledger, and has no picture — the photo lives in the trending
                 block above, so the two never compete. --}}
            @if ($past->isNotEmpty())
                <div class="mt-16">
                    <div class="flex items-end justify-between gap-4 border-b-2 border-slate-900 pb-3" data-reveal>
                        <h3 class="text-lg font-bold tracking-tight text-slate-900 sm:text-xl">{{ $copy['past_title'] }}</h3>
                        <a href="{{ route('berita') }}" class="text-xs font-semibold text-school-700 hover:text-school-800">
                            {{ $copy['latest_archive'] }}
                        </a>
                    </div>

                    <ul class="mt-6 grid grid-cols-1 gap-5 min-[400px]:grid-cols-2 lg:grid-cols-3" data-reveal-group>
                        @foreach ($past as $article)
                            @php
                                $day = $article->published_at?->format('j');
                                $monthYear = $article->published_at
                                    ? (config('school.news.months')[(int) $article->published_at->format('n')] ?? '').' '.$article->published_at->format('Y')
                                    : '';
                            @endphp
                            <li>
                                <a
                                    href="{{ route('berita.show', $article->slug) }}"
                                    class="group flex h-full flex-col rounded-2xl bg-white p-6 ring-1 ring-slate-200 transition duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-school-900/5 hover:ring-school-300 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-school-500"
                                >
                                    <p class="flex items-baseline gap-3">
                                        <span aria-hidden="true" class="text-4xl leading-none font-black text-slate-200 tabular-nums">
                                            {{ $day }}
                                        </span>
                                        <span class="text-xs font-semibold tracking-wide text-slate-500 uppercase">
                                            {{ $monthYear }}
                                        </span>
                                    </p>

                                    <h4 class="mt-4 text-sm leading-snug font-bold text-balance text-slate-900 transition group-hover:text-school-700">
                                        {{ $article->title }}
                                    </h4>

                                    <span class="mt-4 inline-flex items-center gap-1.5 text-xs font-semibold text-school-700">
                                        {{ $copy['read_more'] }}
                                        <x-icon name="arrow-right" class="size-3.5 transition group-hover:translate-x-0.5" />
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ── 3. Trending + jelajahi per kategori ─────────────────────
                 The ranked list leads with a big numeral and shows read counts,
                 and the brick panel carries the category browse. --}}
            <div class="mt-16 grid gap-10 lg:grid-cols-12">
                @if ($trending->isNotEmpty())
                    <div class="lg:col-span-7" data-reveal data-reveal-from="left">
                        <div class="flex items-baseline justify-between gap-4 border-b-2 border-slate-900 pb-3">
                            <h3 class="text-lg font-bold tracking-tight text-slate-900">{{ $copy['trending_title'] }}</h3>
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-school-700 uppercase">
                                <x-icon name="eye" class="size-4" />
                                {{ $copy['trending_note'] }}
                            </span>
                        </div>

                        <ol class="mt-2">
                            @foreach ($trending as $index => $article)
                                <x-news.rank :article="$article" :rank="$index + 1" :views="true" />
                            @endforeach
                        </ol>
                    </div>
                @endif

                <div class="lg:col-span-5" data-reveal data-reveal-from="right">
                    <div class="rounded-3xl bg-brick-green p-7 text-white sm:p-8">
                        <p class="flex items-center gap-3 text-xs font-semibold tracking-[0.18em] text-gold-400 uppercase">
                            <span aria-hidden="true" class="h-px w-8 bg-gold-400"></span>
                            {{ $copy['browse_title'] }}
                        </p>

                        {{-- Each chip carries its label so a reader can see where
                             the archive is busy before clicking through. --}}
                        <div class="mt-5 flex flex-wrap gap-2.5">
                            @foreach ($categories as $slug => $info)
                                <a
                                    href="{{ route('berita', ['kategori' => $slug]) }}"
                                    class="group inline-flex items-center gap-2 rounded-full bg-white/10 px-3.5 py-2 text-sm font-semibold text-white ring-1 ring-white/15 transition hover:bg-white/20 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gold-400"
                                >
                                    <x-icon :name="$info['icon']" class="size-4 text-gold-400" />
                                    {{ $info['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── 4. Kegiatan & Karya Peserta Didik — the seamless photo wall
                 (no gaps, no cards, no numbering) sits straight on the section's
                 own light slate ground; each photo's description only appears
                 when the pointer rests on it, inside the photo itself. --}}
            @if (! empty($galleryPanels))
                <div class="mt-16" data-reveal data-reveal-from="right">
                    <div class="text-center">
                        <p class="inline-flex items-center gap-3 text-xs font-semibold tracking-[0.18em] text-school-700 uppercase">
                            <span aria-hidden="true" class="h-px w-8 bg-gold-500"></span>
                            {{ $copy['eyebrow'] }}
                            <span aria-hidden="true" class="h-px w-8 bg-gold-500"></span>
                        </p>
                        <h3 class="mt-4 text-3xl leading-none font-bold tracking-tight text-slate-900 sm:text-4xl">
                            {{ $copy['gallery_title'] }}
                        </h3>
                    </div>

                    {{-- Six square photos butted flush together, so the wall
                         reads as one connected image; each tile surfaces
                         its title and a short description on hover only. --}}
                    <div class="mt-12 grid grid-cols-2 lg:grid-cols-3">
                        @foreach ($galleryPanels as $panel)
                            @php
                                $photoUrl = App\Support\Images::existingUrl($panel['photo'], 'berita');
                            @endphp
                            <a
                                href="{{ route('berita') }}"
                                class="group relative isolate block aspect-square overflow-hidden focus-visible:relative focus-visible:z-10 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gold-400"
                            >
                                @if ($photoUrl)
                                    <img
                                        src="{{ $photoUrl }}"
                                        alt="{{ $panel['title'] }}"
                                        loading="lazy"
                                        class="absolute inset-0 size-full object-cover transition duration-500 group-hover:scale-[1.06]"
                                    >
                                @else
                                    <span class="absolute inset-0 bg-school-950/70" aria-hidden="true">
                                        <span class="absolute inset-0 flex items-center justify-center text-white/40">
                                            <x-icon name="sparkles" class="size-7" />
                                        </span>
                                    </span>
                                @endif

                                {{-- The description lives inside the photo:
                                     hidden until hover, then it shades the
                                     bottom of the tile and reads over it. --}}
                                <span class="pointer-events-none absolute inset-0 z-10 flex flex-col justify-end bg-gradient-to-t from-school-950/95 via-school-950/55 to-transparent p-4 opacity-0 transition duration-300 group-hover:opacity-100 group-focus-visible:opacity-100">
                                    <span class="text-sm leading-snug font-bold text-white sm:text-base">
                                        {{ $panel['title'] }}
                                    </span>
                                    <span class="mt-1 text-xs leading-snug text-white/85">
                                        {{ $panel['desc'] }}
                                    </span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ── 5. The whole room points at the archive; the button sits
                 after all the stories so the section ends on its final action. --}}
            <div class="mt-16 text-center" data-reveal>
                <a
                    href="{{ route('berita') }}"
                    class="inline-flex items-center gap-2 rounded-full bg-school-700 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-school-800 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gold-400"
                >
                    {{ $copy['all_link'] }}
                    <x-icon name="arrow-right" class="size-4" />
                </a>
            </div>
        @endif
    </div>
</section>