@props([
    'article',
    // A shorter card drops the standfirst — used in the sidebar and in the
    // related strip, where four of them sit side by side.
    'excerpt' => true,
])

@php
    /** @var \App\Models\News $article */
    $info = $article->categoryInfo();
@endphp

<a
    href="{{ route('berita.show', $article->slug) }}"
    @class([
        'group flex h-full flex-col overflow-hidden rounded-2xl bg-white ring-1 ring-school-200 transition duration-300',
        'hover:-translate-y-1 hover:shadow-lg hover:shadow-school-900/5 hover:ring-school-300',
        'focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-school-500',
    ])
>
    <x-news.thumb
        :url="$article->imageUrl()"
        :alt="$article->title"
        :icon="$info['icon']"
        :play="$article->declaresVideo()"
        class="shrink-0"
    />

    <div class="flex flex-1 flex-col p-5">
        <div class="flex items-center justify-between gap-3">
            <x-news.chip :category="$article->category" />

            @if ($article->declaresVideo())
                <span class="inline-flex items-center gap-1 text-[0.68rem] font-semibold tracking-wide text-school-700 uppercase">
                    <x-icon name="play" class="size-3.5" />
                    Video
                </span>
            @endif
        </div>

        <h3 class="mt-3 text-base leading-snug font-bold tracking-tight text-balance text-school-950 transition group-hover:text-school-700 sm:text-lg">
            {{ $article->title }}
        </h3>

        @if ($excerpt && $article->excerpt)
            <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-slate-600">{{ $article->excerpt }}</p>
        @endif

        <x-news.meta :article="$article" class="mt-auto pt-4" />
    </div>
</a>
