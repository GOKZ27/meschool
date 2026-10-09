@props([
    'children',
    'variant' => 'desktop',
    // Programs render as a two-column mega panel; everything else is single column.
    'columns' => 1,
])

@php
    $isDesktop = $variant === 'desktop';
    $isWide = $isDesktop && $columns > 1;
    $current = request()->route()?->getName();
@endphp

<ul @class([
    'overflow-y-auto py-1.5',
    'max-h-[70vh]' => $isDesktop,
    'grid gap-x-1' => $isWide,
    'grid-cols-2' => $isWide,
])>
    @foreach ($children as $child)
        @php $isCurrent = $current === $child['route']; @endphp
        <li>
            <a
                href="{{ route($child['route']) }}"
                @if ($isCurrent)
                    aria-current="page"
                @endif
                @class([
                    'flex items-center gap-2.5 rounded-lg text-sm transition',
                    'px-3 py-2.5 text-slate-600 hover:bg-school-50 hover:text-school-800' => $isDesktop && ! $isCurrent,
                    'px-3 py-2.5 bg-school-50 font-semibold text-school-800' => $isDesktop && $isCurrent,
                    'px-4 py-2.5 text-school-100/90 hover:bg-school-700 hover:text-white' => ! $isDesktop && ! $isCurrent,
                    'px-4 py-2.5 bg-school-700 font-semibold text-white' => ! $isDesktop && $isCurrent,
                ])
            >
                <x-icon
                    name="chevron-right"
                    @class([
                        'size-3.5 shrink-0 transition',
                        'text-school-400 group-hover:text-school-600' => $isDesktop && ! $isCurrent,
                        'text-school-600' => $isDesktop && $isCurrent,
                        'text-school-300' => ! $isDesktop && ! $isCurrent,
                        'text-gold-400' => ! $isDesktop && $isCurrent,
                    ])
                />
                <span>{{ $child['label'] }}</span>
            </a>
        </li>
    @endforeach
</ul>