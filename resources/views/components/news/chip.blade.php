@props([
    'category',
    // Pass a URL to make the chip a link into the filtered archive; leave it
    // null for a plain label (the detail page header, the card corner).
    'href' => null,
])

@php
    // A category that is not in config still has to read as something.
    $info = config('school.news.categories.'.$category)
        + ['label' => ucfirst((string) $category), 'icon' => 'document', 'tone' => 'school'];

    $classes = 'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide ring-1 '.($info['tone'] === 'gold'
        ? 'bg-gold-500/15 text-gold-600 ring-gold-500/30'
        : 'bg-school-50 text-school-700 ring-school-500/25');

    if ($href !== null) {
        $classes .= ' transition hover:ring-school-500/60 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-school-500';
    }
@endphp

@if ($href !== null)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        <x-icon :name="$info['icon']" class="size-3.5 shrink-0" />
        <span>{{ $info['label'] }}</span>
    </a>
@else
    <span {{ $attributes->merge(['class' => $classes]) }}>
        <x-icon :name="$info['icon']" class="size-3.5 shrink-0" />
        <span>{{ $info['label'] }}</span>
    </span>
@endif
