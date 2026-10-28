@props([
    'item',
    'active' => false,
    // Unique per rendered instance: the navbar renders this component twice
    // (desktop bar + mobile drawer), so ids must not collide.
    'uid',
    // Extra classes the caller puts on the root <li>: drawer separators on
    // mobile, the vertical hairline between bar items on desktop.
    'wrapper' => '',
])

@php
    $hasChildren = ! empty($item['children']);
    $columns = (int) ($item['columns'] ?? 1);
    $isExternal = isset($item['url']);
    // Only plain link items carry 'route'; dropdown parents only have 'children'.
    $href = $isExternal ? $item['url'] : (isset($item['route']) ? route($item['route']) : '#');
    $mobileIcon = match ($item['route'] ?? null) {
        'home' => 'home',
        'tenaga-kependidikan' => 'user-group',
        'berita' => 'document',
        'bkk' => 'briefcase',
        'spmb' => 'calendar',
        'kontak' => 'phone',
        default => $isExternal ? 'map-pin' : 'graduation-cap',
    };
@endphp

{{-- This component owns the <li> so the `group` class that drives the hover
     dropdown, the equal-width grid cell, the inter-item hairline, and the
     mobile `last:` separator all stay on one element. --}}
<li @class(['group relative lg:flex lg:items-center', $wrapper])>
    @if ($hasChildren)
        {{-- Desktop: hover / focus-within dropdown. --}}
        <a
            href="#"
            aria-haspopup="true"
            @class([
                'nav-link hidden items-center gap-1 px-3 py-4 text-xs font-semibold tracking-wide whitespace-nowrap uppercase lg:flex lg:w-full lg:justify-center',
                'nav-link-active' => $active,
            ])
        >
            <span>{{ $item['label'] }}</span>
        </a>

        <div
            @class([
                'absolute top-full left-0 z-30 hidden overflow-hidden rounded-b-xl bg-white shadow-xl ring-1 ring-school-200/80 lg:block lg:invisible lg:opacity-0 lg:transition lg:duration-150 lg:group-hover:visible lg:group-hover:opacity-100 lg:group-focus-within:visible lg:group-focus-within:opacity-100',
                'lg:w-[27rem]' => $columns > 1,
                'lg:w-60' => $columns === 1,
            ])
        >
            <x-nav-children :children="$item['children']" variant="desktop" :columns="$columns" />
        </div>

        {{-- Mobile: checkbox + stretched label, so it works with zero JS. --}}
        <div class="relative lg:hidden">
            <input type="checkbox" id="nav-{{ $uid }}" class="peer sr-only" />

            <span @class([
                'nav-mobile-link flex items-center justify-between gap-3',
                'nav-mobile-link--active' => $active,
            ])>
                <span class="nav-mobile-link__main">
                    <x-icon :name="$mobileIcon" class="nav-mobile-link__icon size-7 shrink-0" />
                    <span>{{ $item['label'] }}</span>
                </span>
                <x-icon name="chevron-down" class="nav-mobile-link__chevron size-5 shrink-0 transition-transform duration-200 peer-checked:rotate-180" />
            </span>

            <label
                for="nav-{{ $uid }}"
                class="absolute inset-0 cursor-pointer peer-focus-visible:outline-2 peer-focus-visible:-outline-offset-2 peer-focus-visible:outline-gold-400"
            >
                <span class="sr-only">Buka {{ $item['label'] }}</span>
            </label>

            <div class="hidden border-l-2 border-gold-500/70 bg-school-800 peer-checked:block">
                <x-nav-children :children="$item['children']" variant="mobile" />
            </div>
        </div>
    @else
        <a
            href="{{ $href }}"
            @class([
                'nav-link flex items-center gap-1.5 px-3 py-3 text-xs font-semibold tracking-wide whitespace-nowrap uppercase lg:w-full lg:justify-center lg:py-4',
                'nav-link-active' => $active,
            ])
            @if ($active)
                aria-current="page"
            @endif
            @if ($isExternal)
                target="_blank"
                rel="noopener noreferrer"
            @endif
        >
            <span @class([
                'nav-mobile-link nav-mobile-link--plain',
                'nav-mobile-link--active' => $active,
            ])>
                <x-icon :name="$mobileIcon" class="nav-mobile-link__icon size-7 shrink-0 lg:hidden" />
                <span>{{ $item['label'] }}</span>
            </span>
        </a>
    @endif
</li>