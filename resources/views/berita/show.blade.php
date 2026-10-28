<x-layouts.app
    :title="$article->title . ' — ' . config('school.name')"
    :description="$article->excerpt"
    :image="$article->imageUrl()"
    type="article"
>
    <x-header-top />
    <x-navbar />

    <main>
        @php
            $copy = config('school.news.archive');
            $info = $article->categoryInfo();
            $photo = $article->imageUrl();
            $video = $article->videoUrl();
            $gallery = $article->galleryEntries();

            $logoPath = (string) config('school.logo.path');
            $logo = $logoPath !== '' ? \App\Support\Images::existingUrl($logoPath, 'logo') : null;

            $articleSchema = array_filter([
                '@type' => 'NewsArticle',
                'headline' => $article->title,
                'description' => $article->excerpt,
                'inLanguage' => 'id-ID',
                'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => route('berita.show', $article->slug)],
                'datePublished' => $article->published_at?->toAtomString(),
                'dateModified' => $article->updated_at?->toAtomString(),
                'articleSection' => $article->categoryLabel(),
                'author' => ['@type' => 'Organization', 'name' => $article->author ?: config('school.name')],
                'publisher' => array_filter([
                    '@type' => 'Organization',
                    'name' => config('school.name'),
                    'logo' => $logo ? ['@type' => 'ImageObject', 'url' => $logo] : null,
                ]),
                'image' => $article->imageUrl() ? [$article->imageUrl()] : null,
            ]);

            $breadcrumbSchema = [
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => route('home')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Berita', 'item' => route('berita')],
                    ['@type' => 'ListItem', 'position' => 3, 'name' => $article->categoryLabel(), 'item' => route('berita', ['kategori' => $article->category])],
                    ['@type' => 'ListItem', 'position' => 4, 'name' => $article->title, 'item' => route('berita.show', $article->slug)],
                ],
            ];

            $schema = ['@context' => 'https://schema.org', '@graph' => [$articleSchema, $breadcrumbSchema]];
        @endphp

        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

        <article class="mx-auto max-w-7xl px-4 pt-10 pb-14 sm:px-6 lg:px-8">
            <nav aria-label="Breadcrumb" class="text-sm text-slate-500">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a href="{{ route('home') }}" class="transition hover:text-school-700">Beranda</a></li>
                    <li aria-hidden="true" class="text-slate-300">/</li>
                    <li><a href="{{ route('berita') }}" class="transition hover:text-school-700">Berita</a></li>
                    <li aria-hidden="true" class="text-slate-300">/</li>
                    <li>
                        <a
                            href="{{ route('berita', ['kategori' => $article->category]) }}"
                            class="transition hover:text-school-700"
                        >
                            {{ $article->categoryLabel() }}
                        </a>
                    </li>
                </ol>
            </nav>

            <div class="mt-8 grid gap-12 lg:grid-cols-12">
                {{-- ── The article ──────────────────────────────────────── --}}
                <div class="lg:col-span-8">
                    <header>
                        <x-news.chip :category="$article->category" />

                        <h1 class="mt-4 text-2xl leading-[1.15] font-black tracking-tight text-balance text-school-950 sm:text-4xl">
                            {{ $article->title }}
                        </h1>

                        <x-news.meta :article="$article" author class="mt-5 border-y border-school-200 py-4" />
                    </header>

                    @if ($photo)
                        <figure class="mt-8 overflow-hidden rounded-3xl">
                            <img
                                src="{{ $photo }}"
                                alt="{{ $article->title }}"
                                class="aspect-[16/10] w-full object-cover"
                            >
                        </figure>
                    @else
                        {{-- A missing upload never leaves a broken <img>: the
                             brick texture and the category mark stand in. --}}
                        <figure class="mt-8 overflow-hidden rounded-3xl bg-brick-green">
                            <div class="flex aspect-[16/10] w-full flex-col items-center justify-center gap-3 text-white">
                                <x-icon :name="$info['icon']" class="size-10 text-gold-400" />
                                <span class="text-xs font-semibold tracking-[0.18em] uppercase text-white/70">
                                    {{ $article->categoryLabel() }}
                                </span>
                            </div>
                        </figure>
                    @endif

                    @if ($video)
                        <figure class="mt-8">
                            <video
                                controls
                                playsinline
                                preload="metadata"
                                @if ($photo) poster="{{ $photo }}" @endif
                                class="aspect-video w-full rounded-3xl bg-school-950 object-cover"
                            >
                                <source src="{{ $video }}" type="video/mp4">
                                Browser Anda tidak mendukung pemutaran video.
                            </video>
                        </figure>
                    @endif

                    <div class="mt-8 space-y-5 text-[1.05rem] leading-relaxed text-slate-700">
                        @foreach ($article->paragraphs() as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>

                    @if ($gallery)
                        <section class="mt-10">
                            <h2 class="border-b-2 border-school-950 pb-3 text-base font-bold tracking-tight text-school-950">
                                Galeri
                            </h2>

                            <div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-3">
                                @foreach ($gallery as $photoEntry)
                                    <figure class="overflow-hidden rounded-2xl bg-brick-green ring-1 ring-school-200">
                                        @if ($photoEntry['url'])
                                            <img
                                                src="{{ $photoEntry['url'] }}"
                                                alt="{{ $photoEntry['caption'] }}"
                                                loading="lazy"
                                                class="aspect-square w-full object-cover transition duration-500 hover:scale-[1.06]"
                                            >
                                        @else
                                            <span class="flex aspect-square w-full items-center justify-center text-white/45">
                                                <x-icon name="camera" class="size-7" />
                                            </span>
                                        @endif
                                    </figure>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if ($related->isNotEmpty())
                        <section class="mt-12">
                            <div class="flex items-baseline justify-between gap-4 border-b-2 border-school-950 pb-3">
                                <h2 class="text-base font-bold tracking-tight text-school-950 sm:text-lg">
                                    {{ $copy['related_title'] }}
                                </h2>
                                <a href="{{ route('berita', ['kategori' => $article->category]) }}" class="text-xs font-semibold text-school-700 hover:text-school-800">
                                    Semua {{ $article->categoryLabel() }}
                                </a>
                            </div>

                            <div class="mt-6 grid gap-6 sm:grid-cols-2" data-reveal-group>
                                @foreach ($related as $item)
                                    <x-news.card :article="$item" :excerpt="false" />
                                @endforeach
                            </div>
                        </section>
                    @endif

                    <p class="mt-10">
                        <a
                            href="{{ route('berita') }}"
                            class="inline-flex items-center gap-2 rounded-full bg-school-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-school-800 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gold-400"
                        >
                            <x-icon name="arrow-right" class="size-4 rotate-180" />
                            Kembali ke daftar berita
                        </a>
                    </p>
                </div>

                {{-- ── The rail of most-read stories ────────────────────── --}}
                <aside class="lg:col-span-4">
                    <div class="sticky top-24 space-y-8">
                        @if ($popular->isNotEmpty())
                            <div>
                                <h2 class="flex items-baseline justify-between gap-4 border-b-2 border-school-950 pb-3 text-base font-bold tracking-tight text-school-950">
                                    Trending
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-school-700 uppercase">
                                        <x-icon name="eye" class="size-4" />
                                        Paling banyak dibaca
                                    </span>
                                </h2>

                                <ol class="mt-2">
                                    @foreach ($popular as $index => $item)
                                        <x-news.rank :article="$item" :rank="$index + 1" :views="true" />
                                    @endforeach
                                </ol>
                            </div>
                        @endif

                        <div class="rounded-3xl bg-brick-green p-6 text-white">
                            <p class="flex items-center gap-3 text-xs font-semibold tracking-[0.18em] text-gold-400 uppercase">
                                <span aria-hidden="true" class="h-px w-8 bg-gold-400"></span>
                                Lainnya
                            </p>

                            <div class="mt-4 flex flex-wrap gap-2.5">
                                @foreach (config('school.news.categories') as $slug => $category)
                                    <a
                                        href="{{ route('berita', ['kategori' => $slug]) }}"
                                        class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3.5 py-2 text-sm font-semibold text-white ring-1 ring-white/15 transition hover:bg-white/20"
                                    >
                                        <x-icon :name="$category['icon']" class="size-4 text-gold-400" />
                                        {{ $category['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </article>

        <x-contact-strip />
    </main>
</x-layouts.app>
