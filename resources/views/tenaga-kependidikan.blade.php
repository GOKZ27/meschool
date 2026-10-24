<x-layouts.app
    :title="'Tenaga Kependidikan — '.config('school.name')"
    :description="'Profil tenaga pendidik dan kependidikan '.config('school.name').' yang mendampingi siswa tumbuh dan berprestasi.'"
>
    <x-header-top />
    <x-navbar />

    <main>
        @php
            $staff = config('school.staff');
            $items = array_values(array_filter((array) ($staff['items'] ?? [])));

            // Photos resolve up front exactly like the home rail: a path with no
            // file behind it becomes null so the card falls back to a monogram,
            // and the monogram takes the name in front of the academic title.
            $people = [];

            foreach ($items as $item) {
                $photo = (string) ($item['photo'] ?? '');

                $people[] = [
                    'name' => (string) ($item['name'] ?? ''),
                    'position' => (string) ($item['position'] ?? ''),
                    'description' => (string) ($item['description'] ?? ''),
                    'expertise' => array_values(array_filter((array) ($item['expertise'] ?? []))),
                    'photo' => $photo === '' ? null : (\App\Support\Images::existingUrl($photo, 'guru') ?? \App\Support\Images::existingUrl($photo)),
                    'initials' => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::initials(\Illuminate\Support\Str::before((string) ($item['name'] ?? ''), ','))),
                ];
            }
        @endphp

        {{-- Hero band, the same move as the berita and BKK archives. --}}
        <section class="relative isolate overflow-hidden bg-brick-green">
            <div aria-hidden="true" class="pointer-events-none absolute -top-32 -right-32 size-96 rounded-full bg-white/5 blur-3xl"></div>
            <x-icon name="user-group" class="pointer-events-none absolute -right-14 bottom-0 size-64 rotate-12 text-white/5 sm:size-80" />

            <div class="relative mx-auto max-w-7xl px-4 py-14 sm:px-6 sm:py-16 lg:px-8">
                <div data-reveal-group>
                    <nav aria-label="Jejak halaman" class="flex flex-wrap items-center gap-1.5 text-xs font-medium text-school-100/80">
                        <a href="{{ route('home') }}" class="transition hover:text-white">Beranda</a>
                        <span aria-hidden="true">&middot;</span>
                        <span class="font-semibold text-gold-400" aria-current="page">Tenaga Kependidikan</span>
                    </nav>

                    <p class="mt-6 flex items-center gap-3 text-xs font-semibold tracking-[0.18em] text-gold-400 uppercase">
                        <span aria-hidden="true" class="h-px w-10 bg-gold-400"></span>
                        {{ $staff['eyebrow'] }}
                    </p>

                    <h1 class="mt-4 max-w-3xl text-2xl leading-snug font-bold tracking-tight text-balance text-white sm:text-3xl">
                        {{ $staff['title'] }}
                    </h1>

                    <p class="mt-3 max-w-2xl text-sm leading-relaxed text-school-100/85 sm:text-base">
                        Guru dan staf SMK Islamiyah Ciputat mendampingi siswa belajar, bertumbuh,
                        dan siap menghadapi dunia kerja.
                    </p>

                    <span class="mt-6 inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-xs font-semibold text-white ring-1 ring-white/20">
                        <x-icon name="user-group" class="size-4 text-gold-400" />
                        {{ count($people) }} tenaga pendidik &amp; kependidikan
                    </span>
                </div>
            </div>
        </section>

        {{-- A quiet grid instead of the home rail: no hidden profile popup, so
             the expertise chips finally get to sit on the card itself. --}}
        <section class="bg-white py-20 lg:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <ul class="grid grid-cols-1 gap-6 min-[400px]:grid-cols-2 lg:grid-cols-3" data-reveal-group data-reveal-slow data-staff-grid>
                    @foreach ($people as $person)
                        <li>
                            <article class="group relative flex h-full flex-col overflow-hidden rounded-2xl bg-white ring-1 ring-slate-200 transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:shadow-slate-900/5 hover:ring-school-300">
                                <div aria-hidden="true" class="absolute inset-x-0 top-0 z-10 h-1 bg-gradient-to-r from-gold-500 to-gold-400 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>

                                <div class="relative aspect-4/3 overflow-hidden bg-school-50">
                                    @if ($person['photo'])
                                        <img
                                            src="{{ $person['photo'] }}"
                                            alt="Foto {{ $person['name'] }}"
                                            class="size-full object-cover object-top transition-transform duration-700 ease-out group-hover:scale-105"
                                            loading="lazy"
                                        />
                                    @else
                                        <span
                                            aria-hidden="true"
                                            class="flex size-full items-center justify-center bg-school-700 text-4xl font-bold text-gold-400 transition-transform duration-500 ease-out group-hover:scale-110"
                                        >{{ $person['initials'] }}</span>
                                    @endif
                                </div>

                                <div class="flex flex-1 flex-col p-6">
                                    <h2 class="text-base font-bold tracking-tight text-slate-900">
                                        {{ $person['name'] }}
                                    </h2>

                                    <p class="mt-1.5 flex items-center gap-1.5 text-sm font-semibold text-school-700">
                                        <x-icon name="award" class="size-4 shrink-0 text-gold-500" />
                                        {{ $person['position'] }}
                                    </p>

                                    <p class="mt-3 max-lg:line-clamp-3 text-sm leading-relaxed text-slate-600">
                                        {{ $person['description'] }}
                                    </p>

                                    @if (! empty($person['expertise']))
                                        <ul class="mt-5 flex flex-wrap gap-2">
                                            @foreach ($person['expertise'] as $skill)
                                                <li class="rounded-full bg-gold-500/10 px-3 py-1 text-xs font-semibold text-gold-600 ring-1 ring-gold-500/20">
                                                    {{ $skill }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </article>
                        </li>
                    @endforeach
                </ul>

                <p class="mt-12 flex items-center justify-center gap-2 text-center text-sm text-slate-500" data-reveal>
                    <x-icon name="sparkles" class="size-4 shrink-0 text-gold-500" />
                    Profil guru diperbarui secara berkala mengikuti data resmi tenaga pendidik.
                </p>
            </div>
        </section>

        <x-contact-strip />
    </main>
</x-layouts.app>