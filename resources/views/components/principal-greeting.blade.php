@php
    use App\Support\Images;

    $about = config('school.about');
    $heroPhoto = data_get(config('school.hero.slides'), '0.photo');
    $schoolPhoto = is_string($heroPhoto) && $heroPhoto !== ''
        ? (
            Images::existingUrl($heroPhoto, 'hero')
            ?? Images::existingUrl($heroPhoto, 'principal')
            ?? Images::existingUrl($heroPhoto)
        )
        : null;
    $photoSide = config('school.principal.photo_side') === 'right' ? 'right' : 'left';
    $textSide = $photoSide === 'left' ? 'right' : 'left';
    $titleWords = preg_split('/\s+/', trim($about['title'])) ?: [];
    $titleLastWord = array_pop($titleWords);
    $titleLead = implode(' ', $titleWords);
@endphp

<section id="sambutan" class="relative isolate scroll-mt-24 overflow-hidden bg-gradient-to-br from-slate-50 via-white to-school-50 py-16 sm:py-20 lg:py-24">
    <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 bottom-0 z-0 h-24 sm:h-32">
        <svg class="size-full" viewBox="0 0 1800 180" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path fill="#d9f3e9" fill-opacity=".72" d="M0 24c180 35 237 112 438 107 225-6 347-91 562-79 229 13 319 115 533 92 119-13 190-71 267-97v133H0V24Z"/>
        </svg>
    </div>
    <div aria-hidden="true" class="pointer-events-none absolute -right-10 bottom-5 z-0 hidden h-36 w-56 opacity-50 lg:block">
        <svg class="size-full" viewBox="0 0 220 140" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M8 128 117 18a18 18 0 0 1 13-5h66a12 12 0 0 1 9 20L96 139" stroke="#88d8bb" stroke-width="1.5"/>
        </svg>
    </div>

    <div class="relative z-10 mx-auto grid max-w-[94rem] grid-cols-1 items-center gap-12 px-5 sm:px-8 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)] lg:gap-14 xl:gap-20">
        <figure
            class="relative mx-auto w-full max-w-[42rem] lg:mx-0 lg:max-w-none"
            data-reveal
            data-reveal-from="{{ $photoSide }}"
        >
            <span aria-hidden="true" class="absolute -top-3 -left-3 z-0 h-24 w-24 rounded-[1.75rem] bg-school-100 sm:-top-4 sm:-left-4 sm:h-32 sm:w-32"></span>
            <span aria-hidden="true" class="absolute -right-3 -bottom-3 z-0 h-20 w-24 rounded-[1.75rem] bg-school-100/70 sm:-right-4 sm:-bottom-4 sm:h-28 sm:w-32"></span>
            <svg aria-hidden="true" class="absolute -top-8 right-0 z-10 h-10 w-10 text-school-600 sm:-right-5 sm:-top-9" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M8 13V5m0 0 7 7M8 5h8M23 20l9-9m-4 18h7" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/>
            </svg>

            <div class="relative z-10 aspect-[3/2] overflow-hidden rounded-[1.35rem] bg-school-100 shadow-[0_24px_60px_-30px_rgba(13,72,54,0.32)] ring-1 ring-white/80">
                @if ($schoolPhoto)
                    <img
                        src="{{ $schoolPhoto }}"
                        alt="{{ $about['title'] }}"
                        class="size-full object-cover object-center"
                    />
                @else
                    <div class="flex size-full items-center justify-center bg-brick-green">
                        <x-icon name="home" class="size-14 text-school-200" />
                    </div>
                @endif

                <div class="absolute top-5 left-5 inline-flex items-center gap-2.5 rounded-full bg-white/95 py-2 pl-2.5 pr-5 text-sm font-semibold text-school-800 shadow-lg shadow-school-950/10 sm:top-6 sm:left-6 sm:gap-3 sm:py-2.5 sm:pl-3 sm:pr-6 sm:text-base">
                    <span class="flex size-9 items-center justify-center rounded-full bg-school-700 text-white sm:size-10">
                        <x-icon name="graduation-cap" class="size-5" />
                    </span>
                    Sekolah Unggulan
                </div>
            </div>

            <div class="absolute -right-2 -bottom-7 z-20 flex w-40 rotate-[-5deg] items-center justify-between rounded-2xl bg-white px-4 py-3 shadow-[0_14px_35px_-16px_rgba(13,72,54,0.35)] sm:-right-3 sm:-bottom-8 sm:w-48 sm:px-5 sm:py-4">
                <p class="font-serif text-sm leading-tight font-semibold italic text-school-700 sm:text-base">
                    Berilmu<br>Berakhlak<br>Siap Kerja
                </p>
                <svg aria-hidden="true" class="size-8 shrink-0 text-school-600 sm:size-9" viewBox="0 0 32 32" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M26.7 3.5C16.1 3.8 7.3 7 5 15.1c-1.1 4 1.5 7.5 5.2 7.5 2.5 0 4.1-1.7 5.1-4.1 1.4-3.4 4.2-7.1 7.5-9.2-3.4 3.5-5.8 7.6-6.3 12.1-.3 2.9.4 5.2 2 7.2 7.2-5.2 10.1-14.9 8.2-25.1Z"/>
                </svg>
            </div>
        </figure>

        <div
            class="text-center lg:text-left"
            data-reveal
            data-reveal-from="{{ $textSide }}"
            data-reveal-group
        >
            <p class="inline-flex items-center gap-2 rounded-full bg-school-100 px-4 py-2 text-sm font-semibold text-school-700 sm:text-base">
                <span class="flex size-8 items-center justify-center rounded-full bg-school-700 text-white">
                    <x-icon name="home" class="size-4" />
                </span>
                {{ $about['eyebrow'] }}
            </p>

            <h2 class="mx-auto mt-4 max-w-[32rem] text-4xl leading-[1.04] font-bold tracking-tight text-school-900 sm:text-5xl lg:mx-0 lg:text-[3.5rem]">@if ($titleLead !== '')<span class="inline-block">{{ $titleLead }}</span>&#32;@endif<span class="inline-block text-school-700">{{ $titleLastWord }}</span></h2>

            <p class="mx-auto mt-4 max-w-3xl text-base leading-relaxed text-slate-600 sm:text-lg lg:mx-0">
                {{ config('school.description') }}
            </p>

            <ul class="mx-auto mt-6 grid max-w-3xl grid-cols-3 lg:mx-0">
                @foreach ($about['stats'] as $stat)
                    <li class="flex min-w-0 items-center justify-center gap-2 border-r border-school-200 px-1 text-left last:border-r-0 sm:gap-3 sm:px-3 lg:justify-start">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-school-100 text-school-700 sm:size-14">
                            <x-icon :name="$stat['icon']" class="size-5 sm:size-6" />
                        </span>
                        <span class="min-w-0">
                            <span class="block text-sm leading-tight font-bold text-school-800 sm:text-lg">{{ $stat['value'] }}</span>
                            <span class="block text-[0.65rem] leading-tight text-slate-600 sm:text-sm">{{ $stat['label'] }}</span>
                        </span>
                    </li>
                @endforeach
            </ul>

            <a
                href="{{ route('spmb') }}"
                class="mt-7 inline-flex items-center gap-3 rounded-full bg-school-700 px-7 py-3.5 text-base font-semibold text-white shadow-lg shadow-school-700/15 transition-colors hover:bg-school-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-school-600"
            >
                {{ $about['link_label'] }}
                <x-icon name="arrow-right" class="size-5" />
            </a>
        </div>
    </div>
</section>
