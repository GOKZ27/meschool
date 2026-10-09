@props([
    'article',
    // The author line is long, so it is opt-in: cards skip it, the article
    // page shows it.
    'author' => false,
    'views' => true,
])

@php
    $article = $article instanceof \App\Models\News ? $article : null;
    $date = $article?->dateLabel();
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500']) }}>
    @if ($date)
        <time datetime="{{ $article->published_at->toDateString() }}" class="inline-flex items-center gap-1.5">
            <x-icon name="calendar" class="size-3.5 text-school-600" />
            {{ $date }}
        </time>
    @endif

    @if ($views && $article)
        <span class="inline-flex items-center gap-1.5">
            <x-icon name="eye" class="size-3.5 text-school-600" />
            {{ number_format($article->views, 0, ',', '.') }}x dilihat
        </span>
    @endif

    @if ($author && $article?->author)
        <span class="inline-flex items-center gap-1.5">
            <x-icon name="user-group" class="size-3.5 text-school-600" />
            {{ $article->author }}
        </span>
    @endif
</div>
