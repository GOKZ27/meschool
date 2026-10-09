@php
    use App\Support\Images;
    use Illuminate\Support\Str;

    $staff = config('school.staff');
    $items = array_values(array_filter((array) ($staff['items'] ?? [])));

    // Photos are resolved up front, exactly like the hero and the greeting: a
    // path with no file behind it becomes null, so the card falls back to a
    // monogram instead of rendering a broken image. The monogram is built from
    // the name in front of the academic title, so "Ahmad Fauzan, S.Pd." gives
    // "AF" rather than "AFSPd".
    $people = [];

    foreach ($items as $item) {
        $photo = (string) ($item['photo'] ?? '');

        $people[] = [
            'name' => (string) ($item['name'] ?? ''),
            'position' => (string) ($item['position'] ?? ''),
            'description' => (string) ($item['description'] ?? ''),
            'expertise' => array_values(array_filter((array) ($item['expertise'] ?? []))),
            'photo' => $photo === '' ? null : (Images::existingUrl($photo, 'guru') ?? Images::existingUrl($photo)),
            'initials' => Str::upper(Str::initials(Str::before((string) ($item['name'] ?? ''), ','))),
        ];
    }
@endphp

<section id="tenaga-kependidikan" class="bg-white py-20 lg:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        {{-- Heading on the left, arrows on the right, on one line. The two halves
             travel in from opposite edges so they cross as the reader arrives,
             and the arrows sit with the heading because they act on the rail
             below. --}}
        <div class="flex flex-wrap items-end justify-between gap-6">
            <div data-reveal data-reveal-from="left">
                <p class="flex items-center gap-3 text-xs font-semibold tracking-[0.18em] text-school-700 uppercase">
                    <span aria-hidden="true" class="h-px w-10 bg-gold-500"></span>
                    {{ $staff['eyebrow'] }}
                </p>

                <h2 class="mt-5 max-w-2xl text-2xl leading-snug font-bold tracking-tight text-balance text-slate-900 sm:text-3xl">
                    {{ $staff['title'] }}
                </h2>
            </div>

            {{-- The arrows only mean something once there is more than one card. --}}
            @if (count($people) > 1)
                <div class="flex items-center gap-2" data-reveal data-reveal-from="right">
                    <button
                        type="button"
                        data-staff-prev
                        aria-label="Geser daftar guru ke kiri"
                        class="flex size-11 items-center justify-center rounded-full bg-white text-school-700 ring-1 ring-slate-300 transition hover:bg-school-700 hover:text-white hover:ring-school-700 disabled:pointer-events-none disabled:opacity-35 disabled:hover:bg-white disabled:hover:text-school-700 disabled:hover:ring-slate-300"
                    >
                        <x-icon name="chevron-left" class="size-5" />
                    </button>

                    <button
                        type="button"
                        data-staff-next
                        aria-label="Geser daftar guru ke kanan"
                        class="flex size-11 items-center justify-center rounded-full bg-white text-school-700 ring-1 ring-slate-300 transition hover:bg-school-700 hover:text-white hover:ring-school-700 disabled:pointer-events-none disabled:opacity-35 disabled:hover:bg-white disabled:hover:text-school-700 disabled:hover:ring-slate-300"
                    >
                        <x-icon name="chevron-right" class="size-5" />
                    </button>
                </div>
            @endif
        </div>

    {{-- The rail is a plain horizontal scroller: it scrolls with the wheel
         sideways, snaps card by card, and swipes under a finger on its own.
         The negative margin lets the run of cards reach the right edge of the
         screen, so the next card stays half in view and it is obvious there is
         more to scroll to. There is deliberately no negative margin above it:
         the header above ends on the same line as the arrows, and pulling the
         rail up lands the cards on top of the heading. --}}
    <div class="mt-10">
        <ul
                class="staff-rail -mr-4 flex snap-x snap-mandatory gap-5 overflow-x-auto px-4 pb-4 scroll-pl-4 sm:-mr-6 sm:px-6 sm:scroll-pl-6 lg:-mr-8 lg:px-8 lg:scroll-pl-8"
                data-staff-rail
                tabindex="0"
                aria-label="Daftar tenaga pendidik dan kependidikan"
        >
            @foreach ($people as $index => $person)
                <li class="w-72 shrink-0 snap-start sm:w-80 lg:w-84">
                    {{-- One square portrait box. The name and the role sit over
                         the photo on a bottom gradient; the full description
                         ships hidden because the popup repeats it. --}}
                    <article class="staff-card group relative aspect-square overflow-hidden rounded-2xl bg-school-50 ring-1 ring-slate-200 transition">
                        <div class="staff-photo relative size-full overflow-hidden bg-school-50">
                            @if ($person['photo'])
                                <img
                                    src="{{ $person['photo'] }}"
                                    alt="Foto {{ $person['name'] }}"
                                    class="size-full object-cover object-top"
                                    loading="lazy"
                                />
                            @else
                                {{-- Portrait not uploaded yet: initials on the school
                                     green, which reads as a deliberate placeholder. --}}
                                <span
                                    aria-hidden="true"
                                    class="staff-monogram flex size-full items-center justify-center text-3xl font-bold text-white"
                                >{{ $person['initials'] }}</span>
                            @endif

                            <div class="pointer-events-none absolute inset-x-0 bottom-0 bg-gradient-to-t from-school-950/95 via-school-950/55 to-transparent px-4 pt-14 pb-4">
                                <h3 class="text-base font-bold tracking-tight text-balance text-white">
                                    {{ $person['name'] }}
                                </h3>

                                <p class="mt-1.5 inline-flex items-center rounded-full bg-white/95 px-2.5 py-0.5 text-xs font-semibold text-school-700">
                                    {{ $person['position'] }}
                                </p>
                            </div>
                        </div>

                        <p class="sr-only">{{ $person['description'] }}</p>

                        {{-- One button stretched over the card: the keyboard and touch
                             path, since pointing at a card is what opens it for a
                             mouse. The label stays readable copy underneath. --}}
                        <button
                            type="button"
                            data-staff-open
                            data-staff-index="{{ $index }}"
                            class="absolute inset-0 rounded-2xl focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-school-500"
                        >
                            <span class="sr-only">Lihat profil {{ $person['name'] }}</span>
                        </button>
                    </article>
                </li>
            @endforeach
        </ul>
    </div>
    </div>

    {{-- The dialog and its payload sit outside the container on purpose: the
         overlay is pinned to the viewport, so it has no business inheriting the
         page's max width. --}}
    <dialog class="staff-modal" data-staff-modal aria-labelledby="staff-modal-name">
        <div class="staff-modal-panel relative flex max-h-[85dvh] w-[min(20rem,calc(100vw-2rem))] flex-col overflow-x-hidden overflow-y-auto rounded-2xl bg-white shadow-2xl ring-1 ring-slate-100">
            <button
                type="button"
                data-staff-close
                aria-label="Tutup profil"
                class="absolute top-3 right-3 z-10 flex size-8 items-center justify-center rounded-full bg-white/90 text-slate-700 shadow-sm ring-1 ring-slate-200 transition hover:bg-slate-100 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-school-500"
            >
                <x-icon name="close" class="size-4" />
            </button>

            <div class="staff-modal-photo shrink-0 bg-school-50" data-staff-modal-photo></div>

            <div class="flex flex-1 flex-col p-5">
                <x-icon name="quote" class="staff-modal-quote size-9 text-gold-500" />

                <p class="mt-4 text-sm leading-relaxed text-slate-600" data-staff-modal-description></p>

                <h3 id="staff-modal-name" class="mt-4 pr-8 text-base leading-tight font-bold tracking-tight text-slate-900" data-staff-modal-name></h3>

                <p class="mt-0.5 text-sm font-semibold text-school-700" data-staff-modal-position></p>

                <ul class="staff-modal-expertise mt-4 flex flex-wrap gap-2" data-staff-modal-expertise></ul>
            </div>
        </div>
    </dialog>

    <script type="application/json" data-staff-data>@json($people)</script>
</section>