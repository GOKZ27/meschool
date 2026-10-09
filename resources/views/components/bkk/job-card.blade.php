@props(['job'])

{{-- A horizontal split card: the company logo lives in its own tall box on
     the left, the listing details fill the right, and the whole thing sits on
     a clean white surface with an elegant shadow. On hover the card lifts and
     the shadow deepens. On small screens the logo moves above the copy as a
     wide banner strip instead of squeezing beside it. --}}
<a
    href="{{ route('bkk.show', $job['slug']) }}"
    class="group flex h-full flex-col overflow-hidden rounded-lg bg-white shadow-lg shadow-slate-900/5 ring-1 ring-slate-200 transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-900/15 hover:ring-school-300 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-school-500 sm:flex-row"
>
    {{-- Left: the logo or a monogram banner while no logo file is uploaded —
         the visual anchor of the card. --}}
    <div class="flex shrink-0 items-center justify-center overflow-hidden bg-school-50 p-6 ring-1 ring-slate-100 sm:w-40 sm:p-8 lg:w-52">
        @if ($job['logo_url'])
            <img
                src="{{ $job['logo_url'] }}"
                alt="Logo {{ $job['company'] }}"
                class="size-16 object-contain sm:size-20 lg:size-24"
            >
        @else
            <span
                class="flex size-16 items-center justify-center rounded-lg bg-white text-xl font-black tracking-wide text-school-700 ring-1 ring-school-500/25 sm:size-20 sm:text-2xl"
                aria-hidden="true"
            >
                {{ $job['logo_mark'] }}
            </span>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-5 sm:p-6">
        {{-- [ KATEGORI / BADGE ]  [ Status / jenis pekerjaan ] --}}
        <div class="flex flex-wrap items-center justify-between gap-2">
            @if ($job['category_label'])
                <span class="inline-flex items-center gap-1.5 rounded-full bg-school-50 px-3 py-1 text-xs font-semibold text-school-700 ring-1 ring-school-500/25">
                    <x-icon name="briefcase" class="size-3.5 shrink-0" />
                    {{ $job['category_label'] }}
                </span>
            @endif

            <span class="inline-flex flex-wrap items-center gap-2">
                @if ($job['type'])
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">
                        <x-icon name="check-badge" class="size-3.5 shrink-0 text-slate-400" />
                        {{ $job['type'] }}
                    </span>
                @endif

                <x-bkk.badge :job="$job" />
            </span>
        </div>

        <h3 class="mt-3 text-lg leading-snug font-bold tracking-tight text-balance text-slate-900 transition group-hover:text-school-700 sm:text-xl">
            {{ $job['position'] }}
        </h3>

        <p class="mt-1 text-sm font-semibold text-school-700">{{ $job['company'] }}</p>

        {{-- Lokasi | gaji/fasilitas — the one-line facts of the listing. --}}
        <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-slate-600">
            <span class="inline-flex min-w-0 items-center gap-2">
                <x-icon name="map-pin" class="size-4 shrink-0 text-slate-400" />
                <span class="truncate">{{ $job['location'] }}</span>
            </span>

            @if ($job['salary'])
                <span class="inline-flex min-w-0 items-center gap-2">
                    <x-icon name="banknote" class="size-4 shrink-0 text-slate-400" />
                    <span class="truncate">{{ $job['salary'] }}</span>
                </span>
            @endif
        </div>

        {{-- Footer: deadline on the left, the call to action on the right. --}}
        <div class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4">
            <span class="inline-flex items-center gap-1.5 text-xs text-slate-500">
                <x-icon name="calendar" class="size-4 shrink-0 text-slate-400" />
                Tutup {{ $job['deadline_label'] }}
            </span>

            <span class="inline-flex items-center gap-2 rounded-lg bg-school-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition group-hover:bg-school-800">
                Lihat Detail
                <x-icon name="arrow-right" class="size-4 shrink-0" />
            </span>
        </div>
    </div>
</a>