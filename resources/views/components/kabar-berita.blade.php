@props([
    // A flat, newest-first list of App\Models\News articles — a Collection, an
    // array or a paginator. The wireframe is filled top to bottom from this one
    // pool, so no story is ever printed twice.
    'articles' => null,
    // An optional curated "popular" list for the sidebar. When omitted the next
    // slice of the pool is used instead.
    'popular' => null,
    'allUrl' => null,
    'allLabel' => 'Selengkapnya',
    'eyebrow' => null,
    'title' => null,
    'intro' => null,
])

@php
    use App\Models\News;

    $copy = config('school.news.home', []);
    $categories = config('school.news.categories', []);

    $source = $articles;

    if ($source instanceof \Illuminate\Contracts\Pagination\Paginator) {
        $source = $source->items();
    }

    $pool = collect($source ?? [])
        ->filter(fn ($article) => $article instanceof News)
        ->values();

    // Pull the next $count unused stories off the pool so every layout block is
    // filled in order and the same headline never appears twice.
    $take = function (int $count) use (&$pool) {
        $slice = $pool->take($count);
        $pool = $pool->slice($slice->count())->values();

        return $slice->values();
    };

    $hero = $take(1)->first();

    $popularStories = $popular !== null
        ? collect($popular)->filter(fn ($article) => $article instanceof News)->take(3)->values()
        : $take(3);

    $highlight = $take(1)->first();
    $grid = $take(3);
    $list = $take(4);

    $eyebrowText = $eyebrow ?? ($copy['eyebrow'] ?? 'Kabar Berita');
    $titleText = $title ?? ($copy['title'] ?? 'Kabar Berita');
    $introText = $intro ?? ($copy['intro'] ?? null);
    $allUrl = $allUrl ?? route('berita');
    $hasStories = $hero !== null;
@endphp

{{-- "Kabar Berita" — a magazine/editorial news section built from four very
     different shapes so the page never reads as one card repeated: a lead
     story beside a popular sidebar, a full-width highlight row, an asymmetric
     three-column grid and a compact thumbnail list, closing on a centred
     archive button. Every block is skipped when its slice is empty, and a
     fresh install with no articles still renders. --}}
<section id="kabar-berita" class="bg-slate-50 py-20 lg:py-24" aria-labelledby="kabar-berita-title">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- Masthead: the eyebrow sits between two gold rules and the heading
             runs centred, the quietest way to open the section. --}}
        <div class="mx-auto max-w-2xl text-center" data-reveal>
            <p class="inline-flex items-center gap-3 text-xs font-semibold tracking-[0.18em] text-school-700 uppercase">
                <span aria-hidden="true" class="h-px w-8 bg-gold-500"></span>
                {{ $eyebrowText }}
                <span aria-hidden="true" class="h-px w-8 bg-gold-500"></span>
            </p>

            <h2 id="kabar-berita-title" class="mt-5 text-3xl leading-none font-bold tracking-tight text-balance text-slate-900 sm:text-4xl">
                {{ $titleText }}
            </h2>

            @if ($introText)
                <p class="mt-4 text-base leading-relaxed text-slate-600">{{ $introText }}</p>
            @endif
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
            {{-- ── Layout 1: lead story + popular sidebar ───────────────────
                 The left is the page's largest picture (4:3) with its headline
                 and meta below; the right is a hairline list of popular stories
                 over a row of hashtag links into the filtered archive. --}}
            <div class="mt-12 grid gap-8 lg:grid-cols-12 lg:gap-10">
                <article class="lg:col-span-8" data-kabar-hero data-reveal data-reveal-from="left">
                    <a
                        href="{{ route('berita.show', $hero->slug) }}"
                        class="group block overflow-hidden rounded-3xl bg-white ring-1 ring-slate-200 transition duration-300 hover:ring-school-300 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-school-500"
                    >
                        <x-news.thumb
                            :url="$hero->imageUrl()"
                            :alt="$hero->title"
                            :icon="$hero->categoryInfo()['icon']"
                            :play="$hero->declaresVideo()"
                            aspect="aspect-[4/3]"
                            class="rounded-t-3xl"
                        />

                        <div class="p-6 sm:p-8">
                            <x-news.chip :category="$hero->category" />

                            <h3 class="mt-4 text-xl leading-snug font-bold tracking-tight text-balance text-slate-900 transition group-hover:text-school-700 sm:text-2xl">
                                {{ $hero->title }}
                            </h3>

                            <x-news.meta :article="$hero" :author="true" class="mt-5" />
                        </div>
                    </a>
                </article>

                <div class="lg:col-span-4" data-kabar-popular data-reveal data-reveal-from="right">
                    <div class="flex items-baseline justify-between gap-4 border-b-2 border-slate-900 pb-3">
                        <h3 class="text-lg font-bold tracking-tight text-slate-900">{{ $copy['trending_title'] ?? 'Terpopuler' }}</h3>
                        <a href="{{ $allUrl }}" class="text-xs font-semibold text-school-700 hover:text-school-800">
                            {{ $copy['latest_archive'] ?? 'Semua' }}
                        </a>
                    </div>

                    @if ($popularStories->isNotEmpty())
                        <ul class="mt-2">
                            @foreach ($popularStories as $article)
                                <li>
                                    <a
                                        href="{{ route('berita.show', $article->slug) }}"
                                        class="group flex flex-col gap-2.5 border-b border-slate-200 py-5 transition focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-school-500"
                                    >
                                        <x-news.chip :category="$article->category" :href="route('berita', ['kategori' => $article->category])" class="self-start" />
                                        <span class="text-[0.95rem] leading-snug font-semibold text-balance text-slate-900 transition group-hover:text-school-700">
                                            {{ $article->title }}
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    {{-- Hashtag links, one hop into the filtered archive. --}}
                    @if (! empty($categories))
                        <div class="mt-5 flex flex-wrap gap-2">
                            @foreach ($categories as $slug => $info)
                                <a
                                    href="{{ route('berita', ['kategori' => $slug]) }}"
                                    class="inline-flex items-center gap-1 rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200 transition hover:text-school-700 hover:ring-school-300 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-school-500"
                                >
                                    <span aria-hidden="true">#</span>{{ $info['label'] }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── Layout 2: full-width highlight row ───────────────────────
                 A single story spread across the full width: category on the
                 left, headline running through the middle, and the read link
                 pinned to the right. --}}
            @if ($highlight)
                <article class="mt-10" data-kabar-highlight data-reveal>
                    <a
                        href="{{ route('berita.show', $highlight->slug) }}"
                        class="group flex flex-col gap-4 overflow-hidden rounded-2xl bg-white p-5 ring-1 ring-slate-200 transition duration-300 hover:ring-school-300 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-school-500 sm:flex-row sm:items-center sm:gap-6 sm:p-6"
                    >
                        <x-news.chip :category="$highlight->category" class="self-start shrink-0" />

                        <h3 class="flex-1 text-base leading-snug font-bold text-balance text-slate-900 transition group-hover:text-school-700 sm:text-lg">
                            {{ $highlight->title }}
                        </h3>

                        <span class="inline-flex shrink-0 items-center gap-2 text-sm font-semibold text-school-700 transition group-hover:text-school-800">
                            {{ $copy['read_more'] ?? 'Baca' }} Selengkapnya
                            <x-icon name="arrow-right" class="size-4 transition group-hover:translate-x-0.5" />
                        </span>
                    </a>
                </article>
            @endif

            {{-- ── Layout 3: asymmetric three-column grid ───────────────────
                 Three cards, image on top and category/headline below, for a
                 denser middle band between the lead story and the list. --}}
            @if ($grid->isNotEmpty())
                <div class="mt-10 grid grid-cols-1 gap-6 min-[480px]:grid-cols-2 lg:grid-cols-3" data-kabar-grid data-reveal-group>
                    @foreach ($grid as $article)
                        <article class="h-full">
                            <a
                                href="{{ route('berita.show', $article->slug) }}"
                                class="group flex h-full flex-col overflow-hidden rounded-2xl bg-white ring-1 ring-slate-200 transition duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-school-900/5 hover:ring-school-300 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-school-500"
                            >
                                <x-news.thumb
                                    :url="$article->imageUrl()"
                                    :alt="$article->title"
                                    :icon="$article->categoryInfo()['icon']"
                                    :play="$article->declaresVideo()"
                                    aspect="aspect-[16/10]"
                                />

                                <div class="flex flex-1 flex-col p-5">
                                    <x-news.chip :category="$article->category" class="self-start" />

                                    <h3 class="mt-3 text-sm leading-snug font-bold tracking-tight text-balance text-slate-900 transition group-hover:text-school-700 sm:text-base">
                                        {{ $article->title }}
                                    </h3>

                                    <x-news.meta :article="$article" class="mt-auto pt-4" />
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
            @endif

            {{-- ── Layout 4: compact vertical list ──────────────────────────
                 An optional thumbnail left, headline, excerpt and meta right —
                 the densest row shape, for the remaining stories. --}}
            @if ($list->isNotEmpty())
                <ul class="mt-10 space-y-4" data-kabar-list data-reveal-group>
                    @foreach ($list as $article)
                        <li>
                            <article>
                                <a
                                    href="{{ route('berita.show', $article->slug) }}"
                                    class="group flex gap-4 rounded-2xl bg-white p-4 ring-1 ring-slate-200 transition duration-300 hover:ring-school-300 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-school-500 sm:gap-5 sm:p-5"
                                >
                                    <x-news.thumb
                                        :url="$article->imageUrl()"
                                        :alt="$article->title"
                                        :icon="$article->categoryInfo()['icon']"
                                        :play="$article->declaresVideo()"
                                        aspect="aspect-video"
                                        class="w-28 shrink-0 rounded-xl sm:w-40"
                                    />

                                    <div class="min-w-0 flex-1">
                                        <x-news.chip :category="$article->category" class="self-start" />

                                        <h3 class="mt-2.5 text-sm leading-snug font-semibold text-balance text-slate-900 transition group-hover:text-school-700 sm:text-base">
                                            {{ $article->title }}
                                        </h3>

                                        @if ($article->excerpt)
                                            <p class="mt-1.5 line-clamp-2 text-sm leading-relaxed text-slate-600">
                                                {{ $article->excerpt }}
                                            </p>
                                        @endif

                                        <x-news.meta :article="$article" class="mt-3" />
                                    </div>
                                </a>
                            </article>
                        </li>
                    @endforeach
                </ul>
            @endif

            {{-- The whole section points at the archive; the button sits last
                 so the section ends on its final action. --}}
            <div class="mt-14 text-center" data-reveal>
                <a
                    href="{{ $allUrl }}"
                    class="inline-flex items-center gap-2 rounded-full bg-school-700 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-school-800 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gold-400"
                >
                    {{ $allLabel }}
                    <x-icon name="arrow-right" class="size-4" />
                </a>
            </div>
        @endif
    </div>
</section>
