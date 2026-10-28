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

{{-- The home page previews the news archive as a compact editorial front
     page; each block is skipped when its query is empty. --}}
<section id="berita" class="news-preview bg-school-50 py-20 lg:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="news-preview-heading" data-reveal>
            <div>
                <p class="news-preview-eyebrow">{{ $copy['eyebrow'] }}</p>
                <h2 id="berita-heading">{{ $copy['title'] }}</h2>
            </div>
            <p class="news-preview-intro">{{ $copy['intro'] }}</p>
        </div>

        @if (! $hasStories)
            {{-- Nothing published yet: say so instead of showing empty boxes. --}}
            <div class="news-preview-empty" data-reveal>
                <x-icon name="document" class="size-8 text-school-600" />
                <p class="mt-4 text-sm leading-relaxed text-slate-600">
                    Belum ada berita yang diterbitkan. Kabar terbaru sekolah akan muncul di sini.
                </p>
            </div>
        @else
            <div class="news-preview-feed">
                <div class="news-preview-lead-column">
                    <article class="news-preview-lead" data-reveal data-reveal-from="left">
                        <a href="{{ route('berita.show', $featured->slug) }}" class="news-preview-lead-link">
                            <div class="news-preview-lead-photo">
                                @if ($featured->imageUrl())
                                    <img src="{{ $featured->imageUrl() }}" alt="{{ $featured->title }}" width="1200" height="675" fetchpriority="high">
                                @else
                                    <span class="news-preview-art" aria-hidden="true">
                                        <span class="news-preview-art-mark">{{ str_pad((string) $featured->published_at?->format('d'), 2, '0', STR_PAD_LEFT) }}</span>
                                        <x-icon :name="$featured->categoryInfo()['icon']" class="news-preview-art-icon" />
                                        <span class="news-preview-art-label">{{ $featured->categoryLabel() }}</span>
                                    </span>
                                @endif
                                <span class="news-preview-image-caption">
                                    <span>{{ $featured->categoryLabel() }}</span>
                                </span>
                            </div>

                            <div class="news-preview-lead-copy">
                                <div class="news-preview-kicker">
                                    <span class="news-preview-live-dot" aria-hidden="true"></span>
                                    {{ $copy['featured_label'] }}
                                </div>
                                <h3>{{ $featured->title }}</h3>
                                @if ($featured->excerpt)
                                    <p>{{ $featured->excerpt }}</p>
                                @endif
                                <x-news.meta :article="$featured" class="news-preview-meta" />
                            </div>
                        </a>
                    </article>

                    @if ($grid->isNotEmpty())
                        <div class="news-preview-secondary">
                            @foreach ($grid as $article)
                                <article class="news-preview-secondary-item">
                                    <a href="{{ route('berita.show', $article->slug) }}" class="news-preview-secondary-link">
                                        <span class="news-preview-small-photo">
                                            @if ($article->imageUrl())
                                                <img src="{{ $article->imageUrl() }}" alt="" width="320" height="220" loading="lazy">
                                            @else
                                                <span class="news-preview-small-art" aria-hidden="true">
                                                    <x-icon :name="$article->categoryInfo()['icon']" class="size-6" />
                                                </span>
                                            @endif
                                        </span>
                                        <span class="news-preview-secondary-copy">
                                            <span class="news-preview-category">{{ $article->categoryLabel() }}</span>
                                            <span class="news-preview-secondary-title">{{ $article->title }}</span>
                                            <time datetime="{{ $article->published_at?->toDateString() }}">{{ $article->dateLabel() }}</time>
                                        </span>
                                    </a>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>

                @if ($latest->isNotEmpty())
                    <aside class="news-preview-latest" data-reveal data-reveal-from="right" aria-labelledby="news-preview-latest-title">
                        <div class="news-preview-latest-heading">
                            <h3 id="news-preview-latest-title">{{ $copy['latest_title'] }}</h3>
                            <a href="{{ route('berita') }}">{{ $copy['latest_archive'] }} <span aria-hidden="true">→</span></a>
                        </div>

                        <nav class="news-preview-tabs" aria-label="Filter berita berdasarkan kategori">
                            @foreach ($copy['latest_tabs'] as $slug)
                                @php $tab = $categories[$slug] ?? null; @endphp
                                @if ($tab)
                                    <a href="{{ route('berita', ['kategori' => $slug]) }}">{{ $tab['label'] }}</a>
                                @endif
                            @endforeach
                        </nav>

                        <ol class="news-preview-list">
                            @foreach ($latest as $index => $article)
                                <li>
                                    <a href="{{ route('berita.show', $article->slug) }}" class="news-preview-list-link">
                                        <span class="news-preview-list-number" aria-hidden="true">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                        <span class="news-preview-list-copy">
                                            <span class="news-preview-category">{{ $article->categoryLabel() }}</span>
                                            <span class="news-preview-list-title">{{ $article->title }}</span>
                                            <time datetime="{{ $article->published_at?->toDateString() }}">{{ $article->dateLabel() }}</time>
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ol>
                    </aside>
                @endif
            </div>

            {{-- The latest list paginates the way the BKK archive does: a
                 centred pager of pill buttons — previous, a number per page,
                 next — sitting under the grid and above the archive cards. --}}
            @if ($latest->isNotEmpty() && $latest->hasPages())
                <nav class="mt-10 flex items-center justify-center gap-2" aria-label="Navigasi berita terbaru" data-reveal>
                    @if ($latest->onFirstPage())
                        <span class="inline-flex size-10 items-center justify-center rounded-full bg-white text-slate-300 ring-1 ring-school-200" aria-hidden="true">
                            <x-icon name="chevron-left" class="size-5" />
                        </span>
                    @else
                        <a
                            href="{{ $latest->previousPageUrl() }}"
                            class="inline-flex size-10 items-center justify-center rounded-full bg-white text-slate-700 ring-1 ring-school-200 transition hover:text-school-700 hover:ring-school-300 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-school-500"
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
                                'bg-white text-slate-700 ring-1 ring-school-200 hover:text-school-700 hover:ring-school-300' => $page !== $latest->currentPage(),
                            ])
                            @if ($page === $latest->currentPage()) aria-current="page" @endif
                            aria-label="Halaman {{ $page }}"
                        >
                            {{ $page }}
                        </a>
                    @endfor

                    @if ($latest->onLastPage())
                        <span class="inline-flex size-10 items-center justify-center rounded-full bg-white text-slate-300 ring-1 ring-school-200" aria-hidden="true">
                            <x-icon name="chevron-right" class="size-5" />
                        </span>
                    @else
                        <a
                            href="{{ $latest->nextPageUrl() }}"
                            class="inline-flex size-10 items-center justify-center rounded-full bg-white text-slate-700 ring-1 ring-school-200 transition hover:text-school-700 hover:ring-school-300 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-school-500"
                            aria-label="Halaman berikutnya"
                        >
                            <x-icon name="chevron-right" class="size-5" />
                        </a>
                    @endif
                </nav>
            @endif

            {{-- ── 2. Masa lampau ───────────────────────────────────────── --}}
            @if ($past->isNotEmpty())
                <section class="news-preview-past" aria-labelledby="news-preview-past-title">
                    <div class="news-preview-past-heading" data-reveal>
                        <h3 id="news-preview-past-title">{{ $copy['past_title'] }}</h3>
                        <a href="{{ route('berita') }}">
                            {{ $copy['latest_archive'] }}
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>

                    <ul class="news-preview-past-list" data-reveal-group>
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
                                    class="news-preview-past-link"
                                >
                                    <time class="news-preview-past-date" datetime="{{ $article->published_at?->toDateString() }}">
                                        <span aria-hidden="true">
                                            {{ $day }}
                                        </span>
                                        <span>
                                            {{ $monthYear }}
                                        </span>
                                    </time>

                                    <h4>
                                        {{ $article->title }}
                                    </h4>

                                    <span class="news-preview-past-read">
                                        {{ $copy['read_more'] }}
                                        <span aria-hidden="true">→</span>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            {{-- ── 3. Trending + jelajahi per kategori ───────────────────── --}}
            <div class="news-preview-discovery">
                @if ($trending->isNotEmpty())
                    <section class="news-preview-trending" data-reveal data-reveal-from="left" aria-labelledby="news-preview-trending-title">
                        <div class="news-preview-trending-heading">
                            <h3 id="news-preview-trending-title">{{ $copy['trending_title'] }}</h3>
                            <span>
                                <x-icon name="eye" class="size-4" />
                                {{ $copy['trending_note'] }}
                            </span>
                        </div>

                        <ol class="news-preview-trending-list">
                            @foreach ($trending as $index => $article)
                                <x-news.rank :article="$article" :rank="$index + 1" :views="true" />
                            @endforeach
                        </ol>
                    </section>
                @endif

                <aside class="news-preview-browse" data-reveal data-reveal-from="right" aria-labelledby="news-preview-browse-title">
                    <div class="news-preview-browse-heading">
                        <span aria-hidden="true"></span>
                        <h3 id="news-preview-browse-title">
                            {{ $copy['browse_title'] }}
                        </h3>
                    </div>

                    {{-- Each category is a direct route into the filtered archive. --}}
                    <nav aria-label="Kategori berita">
                        <ul class="news-preview-category-list">
                            @foreach ($categories as $slug => $info)
                                <li>
                                    <a href="{{ route('berita', ['kategori' => $slug]) }}">
                                        <x-icon :name="$info['icon']" class="size-4" />
                                        <span>{{ $info['label'] }}</span>
                                        <span aria-hidden="true">↗</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                </aside>
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
                        <h3 class="mt-4 text-3xl leading-none font-bold tracking-tight text-school-950 sm:text-4xl">
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