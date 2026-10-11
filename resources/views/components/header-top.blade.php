{{-- White utility strip above the main navigation bar. On mobile it collapses
     into the dark navbar (which carries the logo + burger instead), so one
     brand bar is all the phone shows; desktop keeps both. --}}
<header class="hidden border-b border-slate-200 bg-white lg:block">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="ring-school-focus group flex items-center gap-3 rounded-sm">
            {{-- The artwork is portrait (219x274), so its height is pinned and the width
             follows the aspect ratio. A square size-* box would either letterbox
             the mark or stretch the strip; this way the logo fits the header
             instead of the header growing around it. --}}
            <x-logo class="h-9 w-auto shrink-0 transition group-hover:scale-105 sm:h-10" />
            <span class="flex flex-col leading-tight">
                <span class="text-base font-bold tracking-tight text-school-900 sm:text-lg">
                    {{ config('school.name') }}
                </span>
                <span class="hidden text-xs text-slate-500 sm:block">
                    Sekolah Menengah Kejuruan Islamiyah Ciputat
                </span>
            </span>
        </a>

        {{-- Contacts move into the mobile drawer below the lg breakpoint. --}}
        <div class="hidden lg:block">
            <x-contact-links orientation="row" />
        </div>
    </div>
</header>