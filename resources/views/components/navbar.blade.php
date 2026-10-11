@php
    // Only "Program Keahlian" opens a dropdown. The rest are flat links so the
    // bar stays short and nothing has to be hovered to reach a section.
    $items = [
        ['label' => 'BERANDA', 'route' => 'home'],

        [
            'label' => 'PROGRAM KEAHLIAN',
            'columns' => 2,
            'children' => [
                ['label' => 'Kuliner', 'route' => 'program.kuliner'],
                ['label' => 'DKV', 'route' => 'program.dkv'],
                ['label' => 'TKJ', 'route' => 'program.tkj'],
                ['label' => 'AK', 'route' => 'program.ak'],
                ['label' => 'MP', 'route' => 'program.mp'],
                ['label' => 'BD', 'route' => 'program.bd'],
            ],
        ],

        ['label' => 'TENAGA KEPENDIDIKAN', 'route' => 'tenaga-kependidikan'],
        ['label' => 'BERITA', 'route' => 'berita'],
        ['label' => 'BKK', 'route' => 'bkk'],
        ['label' => 'SPMB', 'route' => 'spmb'],
        ['label' => 'KONTAK', 'route' => 'kontak'],

        ['label' => 'MAPS', 'url' => config('school.contact.maps_url')],
    ];

    $current = request()->route()?->getName();

    // A dropdown parent counts as active when one of its children is open.
    $isActive = fn (array $item): bool => $current === ($item['route'] ?? null)
        || collect($item['children'] ?? [])->contains('route', $current);
@endphp

{{-- The two checkboxes live at the top of <nav> so :has() can drive the panels below. --}}
<nav class="nav-root relative z-40 bg-school-700 shadow-sm">
    <input type="checkbox" id="nav-menu" class="sr-only" />
    <input type="checkbox" id="nav-search" class="sr-only" />

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-6 lg:gap-5">
            {{-- Mobile brand: on phones the white header strip above collapses into
                 this bar, so the logo + name live here (lg:hidden keeps them off
                 the desktop bar, where the strip still owns the brand). The name
                 is split one word per line so "SMK ISLAMIYAH CIPUTAT" reads as
                 three tidy rows beside the mark instead of one cramped string. --}}
            <a
                href="{{ route('home') }}"
                aria-label="{{ config('school.name') }}"
                class="flex min-w-0 items-center gap-2.5 lg:hidden"
            >
                <x-logo class="h-8 w-auto shrink-0" surface="light" />
                <span class="flex min-w-0 flex-col leading-[1.1]" aria-hidden="true">
                    @foreach (preg_split('/\s+/', trim((string) config('school.name'))) as $word)
                        <span class="truncate text-[11px] font-bold tracking-[0.08em] text-white uppercase">{{ $word }}</span>
                    @endforeach
                </span>
            </a>

            {{-- Desktop menu: every item takes an equal slice (flex-1), so the hairlines
                 land at even intervals and a short label like BERANDA occupies
                 exactly the same box as PROGRAM KEAHLIAN. --}}
            <ul class="hidden min-w-0 flex-1 items-stretch lg:flex">
                @foreach ($items as $index => $item)
                    <x-nav-item
                        :item="$item"
                        :active="$isActive($item)"
                        uid="desktop-{{ $index }}"
                        wrapper="lg:flex-1 lg:border-white/10 lg:not-first:border-l lg:first:border-l-0"
                    />
                @endforeach
            </ul>

            <div class="flex items-center gap-2">
                {{-- Search: white circle at the far right of the bar. On mobile it
                     only exists inside the drawer, so the bar stays logo + burger. --}}
                <span data-nav-button class="relative hidden size-10 items-center justify-center rounded-full bg-white text-school-700 shadow-sm transition hover:scale-105 hover:bg-school-50 lg:flex">
                    <x-icon name="search" class="size-5" />
                    <label for="nav-search" class="absolute inset-0 cursor-pointer rounded-full">
                        <span class="sr-only">Buka kotak pencarian</span>
                    </label>
                </span>

                {{-- Hamburger: the same 40px square footprint as the search
                     button, with a soft translucent surface so it reads as a
                     control. Both icons sit in the one centred box and swap in
                     place, so opening the drawer never shifts the bar. --}}
                <span data-nav-button class="relative flex size-10 items-center justify-center rounded-lg bg-white/10 text-white ring-1 ring-white/20 transition hover:bg-school-800 hover:ring-white/30 lg:hidden">
                    <x-icon name="menu" data-nav-icon="menu" class="size-5" />
                    <x-icon name="close" data-nav-icon="close" class="hidden size-5" />
                    <label for="nav-menu" class="absolute inset-0 cursor-pointer rounded-lg">
                        <span class="sr-only">Buka menu navigasi</span>
                    </label>
                </span>
            </div>
        </div>
    </div>

    {{-- Mobile drawer: the burger's contents — search on top, then the menu
             items, then the contact channels. --}}
    <div class="nav-menu-panel hidden max-h-[75vh] overflow-y-auto pb-4 lg:hidden">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <form action="{{ route('search') }}" method="GET" class="flex items-center gap-2 py-4">
                <label for="mobile-search-q" class="sr-only">Cari informasi sekolah</label>
                <div class="flex min-w-0 flex-1 items-center gap-2 rounded-full bg-white p-1.5 shadow-sm">
                    <span class="pl-2 text-slate-400">
                        <x-icon name="search" class="size-4" />
                    </span>
                    <input
                        type="search"
                        id="mobile-search-q"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari program keahlian, pengumuman..."
                        class="min-w-0 flex-1 bg-transparent px-1 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none"
                    />
                    <button type="submit" class="flex size-9 shrink-0 items-center justify-center rounded-full bg-school-700 text-white transition hover:bg-school-800">
                        <x-icon name="search" class="size-4" />
                    </button>
                </div>
            </form>

            <ul class="border-t border-school-600/70 pt-2">
                @foreach ($items as $index => $item)
                    <x-nav-item
                        :item="$item"
                        :active="$isActive($item)"
                        uid="mobile-{{ $index }}"
                        wrapper="border-b border-school-600/30 last:border-0"
                    />
                @endforeach
            </ul>

            <div class="mt-4 rounded-xl bg-school-800/60 p-4">
                <x-contact-links orientation="stack" />
            </div>
        </div>
    </div>

    {{-- Search panel --}}
    <div class="nav-search-panel hidden border-t border-school-600 bg-school-800">
        <form action="{{ route('search') }}" method="GET" class="mx-auto max-w-2xl px-4 py-4 sm:px-6 lg:px-8">
            <label for="site-search" class="sr-only">Cari informasi sekolah</label>
            <div class="flex items-center gap-2 rounded-full bg-white p-1.5 shadow-sm">
                <input
                    type="search"
                    id="site-search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari program keahlian, pengumuman, informasi..."
                    class="min-w-0 flex-1 bg-transparent px-3 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none"
                />
                <button type="submit" class="flex size-9 shrink-0 items-center justify-center rounded-full bg-school-700 text-white transition hover:bg-school-800">
                    <x-icon name="search" class="size-4" />
                </button>
            </div>
        </form>
    </div>
</nav>