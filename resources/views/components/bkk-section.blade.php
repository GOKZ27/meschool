@props(['jobs'])

@php
    $copy = config('school.bkk.home');
@endphp

{{-- The home page preview of the BKK archive. The masthead halves cross from
     opposite edges like the programs and news sections, the square card grid
     cascades in the slower rhythm, and the whole-pointing CTA sits centred
     under the cards so the row is never cramped by a button in the masthead. --}}
<section id="bkk" class="bg-white py-20 lg:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-7" data-reveal data-reveal-from="left">
                <p class="flex items-center gap-3 text-xs font-semibold tracking-[0.18em] text-school-700 uppercase">
                    <span aria-hidden="true" class="h-px w-10 bg-gold-500"></span>
                    {{ $copy['eyebrow'] }}
                </p>

                <h2 class="mt-5 text-2xl leading-snug font-bold tracking-tight text-balance text-slate-900 sm:text-3xl">
                    {{ $copy['title'] }}
                </h2>
            </div>

            <div class="lg:col-span-5" data-reveal data-reveal-from="right">
                <p class="text-base leading-relaxed text-slate-600">{{ $copy['intro'] }}</p>
            </div>
        </div>

        @if ($jobs->isEmpty())
            {{-- No listings configured yet: say so instead of showing empty boxes. --}}
            <div class="mt-12 rounded-2xl bg-slate-50 p-10 text-center ring-1 ring-slate-200" data-reveal>
                <x-icon name="briefcase" class="size-8 text-school-600" />
                <p class="mt-4 text-sm leading-relaxed text-slate-600">
                    Belum ada lowongan yang dibuka. Info karier terbaru akan muncul di sini.
                </p>
            </div>
        @else
            <ul class="mt-12 grid grid-cols-1 gap-6 min-[400px]:grid-cols-2" data-reveal-group data-reveal-slow>
                @foreach ($jobs as $job)
                    <li>
                        <x-bkk.job-card :job="$job" />
                    </li>
                @endforeach
            </ul>

            {{-- The CTA points at the whole archive, so it belongs under the
                 cards where the eye lands after the row — not beside the masthead. --}}
            <div class="mt-10 text-center" data-reveal>
                <a
                    href="{{ route('bkk') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-school-700 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-school-800 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gold-400"
                >
                    {{ $copy['all_link'] }}
                    <x-icon name="arrow-right" class="size-4" />
                </a>
            </div>
        @endif
    </div>
</section>