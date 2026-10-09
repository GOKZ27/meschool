@php
    use App\Support\Images;

    $principal = config('school.principal');

    // Same graceful fallback as the hero: no portrait on disk means a plain
    // panel, never a broken image in the middle of the greeting.
    $photo = Images::existingUrl((string) $principal['photo'], 'principal')
        ?? Images::existingUrl((string) $principal['photo']);

    $paragraphs = array_values(array_filter((array) $principal['message']));

    // Which side the portrait sits on comes from config, so re-balancing the
    // block is a one-word edit rather than a markup rewrite. DOM order stays
    // portrait-then-text, and only the visual order flips.
    $portraitOnLeft = $principal['photo_side'] === 'left';

    // The two halves enter from opposite edges, so the portrait slides in from
    // its own side and the text from the other — they read as crossing over.
    $portraitFrom = $portraitOnLeft ? 'left' : 'right';
    $textFrom = $portraitOnLeft ? 'right' : 'left';
@endphp

{{--
    Greeting from the principal, sitting between the hero banner and the contact
    strip. Copy and portrait come from config('school.principal'); the entrance is
    a scroll reveal, with data-reveal in resources/css/app.css doing the motion
    and resources/js/app.js only deciding when it has been scrolled into view.
--}}
<section id="sambutan" class="scroll-mt-24 bg-white py-20 lg:py-28">
    <div class="mx-auto grid max-w-7xl gap-12 px-4 sm:px-6 lg:grid-cols-12 lg:items-center lg:gap-16 lg:px-8">
        <figure
            @class(['relative lg:col-span-5', 'lg:order-1' => $portraitOnLeft, 'lg:order-2' => ! $portraitOnLeft])
            data-reveal
            data-reveal-from="{{ $portraitFrom }}"
        >
            {{-- An outlined frame nudged off the portrait, so the corner detail
                 reads as a deliberate crop rather than a stray border. --}}
            <span aria-hidden="true" class="absolute -top-5 -right-5 hidden h-full w-full rounded-2xl border border-gold-400/50 sm:block"></span>

            <div class="relative aspect-4/5 overflow-hidden rounded-2xl bg-school-50 ring-1 ring-slate-200">
                @if ($photo)
                    <img
                        src="{{ $photo }}"
                        alt="{{ $principal['name'] ? $principal['name'].', '.$principal['role'] : $principal['role'].' SMK Islamiyah Ciputat' }}"
                        class="size-full object-cover object-top"
                    />
                @else
                    {{-- Portrait not uploaded yet: a quiet panel that still reads as
                         a frame. No invented name, no "coming soon" copy. --}}
                    <div class="flex size-full items-center justify-center bg-brick-green">
                        <x-icon name="graduation-cap" class="size-12 text-gold-400" />
                    </div>
                @endif
            </div>

            <figcaption class="mt-5 flex items-center gap-3 text-sm text-slate-500">
                <span aria-hidden="true" class="h-px w-10 shrink-0 bg-gold-400"></span>
                {{ $principal['role'] }} {{ config('school.name') }}
            </figcaption>
        </figure>

        <div
            @class(['lg:col-span-7', 'lg:order-1' => ! $portraitOnLeft, 'lg:order-2' => $portraitOnLeft])
            data-reveal
            data-reveal-from="{{ $textFrom }}"
            data-reveal-group
        >
            <p class="flex items-center gap-3 text-xs font-semibold tracking-[0.18em] text-school-700 uppercase">
                <span aria-hidden="true" class="h-px w-10 bg-gold-500"></span>
                Sambutan {{ $principal['role'] }}
            </p>

            <h2 class="mt-5 text-2xl leading-snug font-bold tracking-tight text-balance text-slate-900 sm:text-3xl">
                Kami ingin sekolah ini menjadi tempat setiap anak merasa aman untuk mencoba.
            </h2>

            @foreach ($paragraphs as $paragraph)
                <p class="mt-4 text-base leading-relaxed text-slate-600">
                    {{ $paragraph }}
                </p>
            @endforeach

            {{-- Signature. Without a name configured the role stands alone, so
                 the block never credits a principal nobody has confirmed yet. --}}
            <div class="mt-8">
                <p class="text-base font-semibold text-slate-900">
                    {{ $principal['name'] ?? $principal['role'] }}
                </p>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $principal['name'] ? $principal['role'] : config('school.name') }}
                </p>
            </div>
        </div>
    </div>
</section>
