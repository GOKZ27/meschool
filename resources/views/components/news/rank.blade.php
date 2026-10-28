@props([
    'article',
    // The position in the list, drawn as a big numeral beside the headline.
    'rank' => 1,
    // Trending renders first and is judged on read counts, so views are opt-in.
    'views' => false,
])

@php
    /** @var \App\Models\News $article */
    $position = str_pad((string) $rank, 2, '0', STR_PAD_LEFT);
@endphp

<li class="flex items-start gap-4 border-b border-school-200 py-4 last:border-0">
    <span aria-hidden="true" class="w-8 shrink-0 text-2xl leading-none font-black text-school-200 tabular-nums">
        {{ $position }}
    </span>

    <div class="min-w-0 flex-1">
        <a
            href="{{ route('berita.show', $article->slug) }}"
            class="text-sm leading-snug font-semibold text-school-950 transition hover:text-school-700 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-school-500"
        >
            {{ $article->title }}
        </a>

        <div class="mt-2">
            <x-news.meta :article="$article" :views="$views" />
        </div>
    </div>

    <span class="hidden shrink-0 sm:block">
        <x-news.chip :category="$article->category" />
    </span>
</li>
