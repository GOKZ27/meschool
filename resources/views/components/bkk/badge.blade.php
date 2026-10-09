@props(['job'])

@php
    // The palette follows the news chips: green for live listings, gold for
    // the "closing soon" edge, slate once the deadline has passed.
    $tone = match ($job['status']) {
        'open' => ['classes' => 'bg-school-50 text-school-700 ring-school-500/25', 'icon' => 'check-badge'],
        'closing' => ['classes' => 'bg-gold-500/15 text-gold-600 ring-gold-500/30', 'icon' => 'clock'],
        default => ['classes' => 'bg-slate-100 text-slate-500 ring-slate-300', 'icon' => 'close'],
    };
@endphp

<span
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide ring-1 '.$tone['classes']]) }}
>
    <x-icon :name="$tone['icon']" class="size-3.5 shrink-0" />
    {{ $job['status_label'] }}
</span>