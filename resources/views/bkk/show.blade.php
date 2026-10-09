<x-layouts.app :title="$job['position'] . ' — ' . config('school.name')">
    <x-header-top />
    <x-navbar />

    <main>
        @php
            $copy = config('school.bkk.detail');
            $isRemote = str_starts_with($job['apply_url'], 'http');
        @endphp

        <div class="mx-auto max-w-7xl px-4 pt-10 pb-14 sm:px-6 lg:px-8">
            <nav aria-label="Breadcrumb" class="text-sm text-slate-500">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a href="{{ route('home') }}" class="transition hover:text-school-700">Beranda</a></li>
                    <li aria-hidden="true" class="text-slate-300">/</li>
                    <li><a href="{{ route('bkk') }}" class="transition hover:text-school-700">BKK</a></li>
                </ol>
            </nav>

            {{-- ── The header: who hires, for what, and by when ─────────── --}}
            <header class="mt-8 flex flex-col gap-6 border-b border-slate-200 pb-8 sm:flex-row sm:items-start">
                <x-bkk.logo :job="$job" size="lg" />

                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl leading-[1.15] font-black tracking-tight text-balance text-slate-900 sm:text-3xl">
                        {{ $job['position'] }}
                    </h1>

                    <p class="mt-2 text-base font-semibold text-school-700">{{ $job['company'] }}</p>

                    <dl class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-slate-600">
                        <div class="flex items-center gap-1.5">
                            <dt class="sr-only">Lokasi</dt>
                            <dd class="flex items-center gap-1.5">
                                <x-icon name="map-pin" class="size-4 text-slate-400" />
                                {{ $job['location'] }}
                            </dd>
                        </div>

                        <div class="flex items-center gap-1.5">
                            <dt class="sr-only">Jenis pekerjaan</dt>
                            <dd class="flex items-center gap-1.5">
                                <x-icon name="briefcase" class="size-4 text-slate-400" />
                                {{ $job['type'] }}
                                @if ($job['salary'])
                                    <span class="text-slate-400">&middot;</span>
                                    <span>{{ $job['salary'] }}</span>
                                @endif
                            </dd>
                        </div>

                        <dt class="sr-only">Status pendaftaran</dt>
                        <dd><x-bkk.badge :job="$job" /></dd>
                    </dl>
                </div>
            </header>

            <div class="mt-10 grid gap-12 lg:grid-cols-12">
                {{-- ── The listing ──────────────────────────────────────── --}}
                <div class="lg:col-span-8">
                    <section>
                        <h2 class="border-b-2 border-slate-900 pb-3 text-base font-bold tracking-tight text-slate-900">
                            {{ $copy['description'] }}
                        </h2>

                        <div class="mt-5 space-y-4 text-[1.02rem] leading-relaxed text-slate-700">
                            @foreach ($job['description'] as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    </section>

                    @if ($job['requirements'])
                        <section class="mt-10">
                            <h2 class="border-b-2 border-slate-900 pb-3 text-base font-bold tracking-tight text-slate-900">
                                {{ $copy['requirements'] }}
                            </h2>

                            <ul class="mt-5 list-disc space-y-2 pl-5 text-slate-700">
                                @foreach ($job['requirements'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </section>
                    @endif

                    @if ($job['qualifications'])
                        <section class="mt-10">
                            <h2 class="border-b-2 border-slate-900 pb-3 text-base font-bold tracking-tight text-slate-900">
                                {{ $copy['qualifications'] }}
                            </h2>

                            <ul class="mt-5 space-y-2.5">
                                @foreach ($job['qualifications'] as $item)
                                    <li class="flex gap-3 text-slate-700">
                                        <x-icon name="check-badge" class="mt-0.5 size-5 shrink-0 text-school-600" />
                                        <span>{{ $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endif

                    @if ($job['benefits'])
                        <section class="mt-10">
                            <h2 class="border-b-2 border-slate-900 pb-3 text-base font-bold tracking-tight text-slate-900">
                                {{ $copy['benefits'] }}
                            </h2>

                            <ul class="mt-5 space-y-2.5">
                                @foreach ($job['benefits'] as $item)
                                    <li class="flex gap-3 text-slate-700">
                                        <x-icon name="check-badge" class="mt-0.5 size-5 shrink-0 text-gold-600" />
                                        <span>{{ $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endif

                    @if ($job['how_to_apply'])
                        <section class="mt-10">
                            <h2 class="border-b-2 border-slate-900 pb-3 text-base font-bold tracking-tight text-slate-900">
                                {{ $copy['how_to_apply'] }}
                            </h2>

                            <ol class="mt-5 space-y-3">
                                @foreach ($job['how_to_apply'] as $index => $step)
                                    <li class="flex gap-3">
                                        <span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-school-700 text-xs font-bold text-white">
                                            {{ $index + 1 }}
                                        </span>
                                        <span class="leading-relaxed text-slate-700">{{ $step }}</span>
                                    </li>
                                @endforeach
                            </ol>
                        </section>
                    @endif

                    <p class="mt-10">
                        <a
                            href="{{ route('bkk') }}"
                            class="inline-flex items-center gap-2 rounded-full bg-school-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-school-800 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gold-400"
                        >
                            <x-icon name="arrow-right" class="size-4 rotate-180" />
                            Kembali ke daftar lowongan
                        </a>
                    </p>
                </div>

                {{-- ── The apply rail ───────────────────────────────────── --}}
                <aside class="lg:col-span-4">
                    <div class="sticky top-24 space-y-8">
                        <div class="rounded-2xl bg-white p-6 ring-1 ring-slate-200">
                            <h2 class="text-base font-bold tracking-tight text-slate-900">
                                {{ $copy['overview'] }}
                            </h2>

                            <dl class="mt-4 space-y-3 text-sm">
                                <div class="flex items-center justify-between gap-4">
                                    <dt class="text-slate-500">Status</dt>
                                    <dd><x-bkk.badge :job="$job" /></dd>
                                </div>

                                <div class="flex items-center justify-between gap-4">
                                    <dt class="text-slate-500">{{ $copy['deadline_label'] }}</dt>
                                    <dd class="font-semibold text-slate-900">{{ $job['deadline_label'] }}</dd>
                                </div>

                                <div class="flex items-center justify-between gap-4">
                                    <dt class="text-slate-500">Dibuka</dt>
                                    <dd class="font-semibold text-slate-900">{{ $job['published_label'] }}</dd>
                                </div>

                                <div class="flex items-center justify-between gap-4">
                                    <dt class="text-slate-500">Kategori</dt>
                                    <dd class="font-semibold text-slate-900">{{ $job['category_label'] }}</dd>
                                </div>

                                <div class="flex items-center justify-between gap-4">
                                    <dt class="text-slate-500">Lokasi</dt>
                                    <dd class="text-right font-semibold text-slate-900">{{ $job['location'] }}</dd>
                                </div>
                            </dl>

                            @if ($job['is_active'])
                                <a
                                    href="{{ $job['apply_url'] }}"
                                    @if ($isRemote) target="_blank" rel="noopener" @endif
                                    class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-full bg-gold-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-gold-600 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gold-400"
                                >
                                    {{ $copy['apply_label'] }}
                                    <x-icon name="arrow-right" class="size-4" />
                                </a>
                            @else
                                <p class="mt-6 rounded-xl bg-slate-100 px-4 py-3 text-center text-sm font-semibold text-slate-500">
                                    Lowongan ini sudah ditutup
                                </p>
                            @endif

                            <p class="mt-4 text-center text-xs leading-relaxed text-slate-500">
                                Punya pertanyaan? Hubungi tim BKK SMK Islamiyah Ciputat melalui media
                                sosial atau kontak di bawah halaman.
                            </p>
                        </div>

                        @if ($related->isNotEmpty())
                            <div>
                                <h2 class="border-b-2 border-slate-900 pb-3 text-base font-bold tracking-tight text-slate-900">
                                    Lowongan Terkait
                                </h2>

                                <ul class="mt-3">
                                    @foreach ($related as $other)
                                        <li>
                                            <a
                                                href="{{ route('bkk.show', $other['slug']) }}"
                                                class="flex items-start justify-between gap-4 border-b border-slate-200 py-4 transition focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-school-500"
                                            >
                                                <span class="min-w-0">
                                                    <span class="flex items-center gap-2">
                                                        <x-bkk.badge :job="$other" />
                                                    </span>
                                                    <span class="mt-2 block text-[0.95rem] leading-snug font-semibold text-slate-900 transition hover:text-school-700">
                                                        {{ $other['position'] }}
                                                    </span>
                                                    <span class="mt-1 block text-sm text-slate-500">{{ $other['company'] }}</span>
                                                </span>
                                                <x-icon name="chevron-right" class="mt-1 size-4 shrink-0 text-slate-400" />
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </aside>
            </div>
        </div>

        <x-contact-strip />
    </main>
</x-layouts.app>