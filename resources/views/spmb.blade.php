<x-layouts.app
    :title="'SPMB — '.config('school.name')"
    :description="config('school.spmb_page.intro')"
>
    <x-header-top />
    <x-navbar />

    <main>
        @php
            $page = config('school.spmb_page');
            $banner = config('school.spmb');

            $path = (string) ($banner['bg_photo'] ?? '');
            $bg = $path !== ''
                ? (\App\Support\Images::existingUrl($path, 'spmb') ?? \App\Support\Images::existingUrl($path))
                : null;

            $stepIcons = ['pencil-square', 'document', 'check-badge', 'graduation-cap'];
        @endphp

        {{-- The landing page opens on the same photo band as the home banner,
             so the two places read as one campaign. --}}
        <section class="relative isolate flex min-h-[520px] items-center overflow-hidden bg-school-950 py-24 sm:py-28 lg:min-h-[600px]">
            @if ($bg)
                <div
                    aria-hidden="true"
                    data-spmb-bg
                    class="absolute inset-0 -z-20 bg-cover bg-center"
                    style="background-image:url('{{ $bg }}')"
                ></div>
            @else
                <div aria-hidden="true" class="absolute inset-0 -z-20 bg-gradient-to-br from-school-700 via-school-800 to-school-950"></div>
            @endif

            <div
                aria-hidden="true"
                data-spmb-overlay
                class="absolute inset-0 -z-10 {{ $banner['overlay'] }}"
            ></div>

            <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 bottom-0 top-1/2 -z-10 bg-gradient-to-t from-school-950/80 via-transparent to-transparent"></div>

            <div class="relative mx-auto w-full max-w-5xl px-4 text-center sm:px-6" data-reveal-group>
                <p class="flex items-center justify-center gap-3 text-xs font-semibold tracking-[0.18em] text-gold-400 uppercase">
                    <span aria-hidden="true" class="h-px w-10 bg-gold-500"></span>
                    {{ $page['hero_eyebrow'] }}
                    <span aria-hidden="true" class="h-px w-10 bg-gold-500"></span>
                </p>

                <h1 class="mt-6 text-3xl leading-tight font-extrabold tracking-tight text-balance text-white sm:text-4xl lg:text-5xl">
                    {{ $page['title'] }}
                </h1>

                <p class="mt-3 text-lg font-semibold tracking-wide text-gold-300 sm:text-xl">
                    {{ $page['subtitle'] }}
                </p>

                <p class="mx-auto mt-6 max-w-[50rem] text-base leading-relaxed text-school-100/90 sm:text-lg">
                    {{ $page['intro'] }}
                </p>

                <div class="mt-9 flex flex-wrap items-center justify-center gap-3">
                    <a
                        href="{{ config('school.contact.whatsapp_link') }}"
                        class="group inline-flex items-center gap-2 rounded-full bg-gold-500 px-7 py-3 text-sm font-bold text-white shadow-lg shadow-gold-500/30 transition-all hover:-translate-y-0.5 hover:bg-gold-600 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gold-400"
                    >
                        <x-icon name="whatsapp" class="size-4" />
                        {{ $page['register_label'] }}
                        <x-icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-0.5" />
                    </a>

                    <a
                        href="{{ route('kontak') }}"
                        class="inline-flex items-center gap-2 rounded-full bg-white/10 px-7 py-3 text-sm font-bold text-white ring-1 ring-white/25 transition-all hover:-translate-y-0.5 hover:bg-white/20 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-white"
                    >
                        <x-icon name="calendar" class="size-4" />
                        {{ $page['contact_label'] }}
                    </a>
                </div>
            </div>

            <a
                href="#alur"
                class="absolute bottom-6 left-1/2 hidden -translate-x-1/2 items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-semibold text-white/80 ring-1 ring-white/20 transition hover:bg-white/20 sm:inline-flex"
            >
                Lihat Alur
                <x-icon name="chevron-down" class="size-4 animate-bounce motion-reduce:animate-none" />
            </a>
        </section>

        {{-- The registration flow, step by step. --}}
        <section id="alur" class="scroll-mt-24 bg-white py-20 lg:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="max-w-2xl" data-reveal data-reveal-from="left">
                    <p class="flex items-center gap-3 text-xs font-semibold tracking-[0.18em] text-school-700 uppercase">
                        <span aria-hidden="true" class="h-px w-10 bg-gold-500"></span>
                        {{ $page['steps_label'] }}
                    </p>

                    <h2 class="mt-5 text-2xl leading-snug font-bold tracking-tight text-balance text-slate-900 sm:text-3xl">
                        Empat langkah menuju kelas baru Anda
                    </h2>

                    <p class="mt-4 text-sm leading-relaxed text-slate-600 sm:text-base">
                        Ikuti setiap tahapan sesuai urutan. Panitia SPMB siap membantu di tiap
                        langkah, sampai pengumuman dan daftar ulang selesai.
                    </p>
                </div>

                <ol class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4" data-reveal-group data-reveal-slow data-spmb-steps>
                    @foreach ($page['steps'] as $index => $step)
                        <li class="relative">
                            <article class="group relative h-full rounded-2xl bg-slate-50 p-6 ring-1 ring-slate-200 transition-all duration-300 hover:-translate-y-1.5 hover:bg-white hover:shadow-xl hover:shadow-slate-900/5 hover:ring-gold-400/40">
                                <span class="relative flex size-12 items-center justify-center rounded-2xl bg-school-700 text-gold-400 shadow-md transition-colors duration-300 group-hover:bg-gold-500 group-hover:text-white">
                                    <x-icon :name="$stepIcons[$index] ?? 'sparkles'" class="size-6" />
                                    <span class="absolute -top-2 -right-2 flex size-5 items-center justify-center rounded-full bg-gold-500 text-[10px] font-bold text-white ring-2 ring-white">
                                        {{ $index + 1 }}
                                    </span>
                                </span>

                                <h3 class="mt-5 text-base leading-snug font-bold tracking-tight text-slate-900">
                                    {{ $step['title'] }}
                                </h3>

                                <p class="mt-2 text-sm leading-relaxed text-slate-600">
                                    {{ $step['description'] }}
                                </p>
                            </article>

                            @unless ($loop->last)
                                <span
                                    aria-hidden="true"
                                    class="absolute top-12 left-18 -right-6 hidden h-0.5 bg-gradient-to-r from-school-200 via-school-200 to-gold-500/60 lg:block"
                                ></span>
                            @endunless
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>

        {{-- What goes in the file. --}}
        <section class="bg-slate-50 py-20 lg:py-24">
            <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 sm:px-6 lg:grid-cols-12 lg:gap-14 lg:px-8">
                <div class="lg:col-span-5" data-reveal data-reveal-from="left">
                    <span class="flex size-14 items-center justify-center rounded-2xl bg-school-700 text-gold-400 shadow-lg shadow-school-700/20">
                        <x-icon name="document" class="size-7" />
                    </span>

                    <p class="mt-6 flex items-center gap-3 text-xs font-semibold tracking-[0.18em] text-school-700 uppercase">
                        <span aria-hidden="true" class="h-px w-10 bg-gold-500"></span>
                        {{ $page['requirements_label'] }}
                    </p>

                    <h2 class="mt-5 text-2xl leading-snug font-bold tracking-tight text-balance text-slate-900 sm:text-3xl">
                        Siapkan berkas pendaftaran Anda
                    </h2>

                    <p class="mt-4 text-sm leading-relaxed text-slate-600 sm:text-base">
                        Pastikan seluruh dokumen sudah lengkap dan mudah dibaca sebelum diserahkan
                        ke panitia. Daftar ini mengikuti ketentuan yang berlaku tahun ajaran berjalan.
                    </p>
                </div>

                <div class="lg:col-span-7">
                    <ul class="grid gap-3 sm:grid-cols-2" data-reveal-group data-reveal-slow data-spmb-requirements>
                        @foreach ($page['requirements'] as $requirement)
                            <li class="group flex items-center gap-3 rounded-xl bg-white p-4 ring-1 ring-slate-200 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md hover:shadow-slate-900/5 hover:ring-gold-400/40">
                                <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-gold-500/10 text-gold-600 transition-colors duration-200 group-hover:bg-gold-500 group-hover:text-white">
                                    <x-icon name="check-badge" class="size-4" />
                                </span>
                                <span class="text-sm leading-relaxed font-medium text-slate-700">{{ $requirement }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </section>

        {{-- Next step: pick a program or start registering. --}}
        <section class="relative isolate overflow-hidden bg-school-950 py-16 lg:py-20" data-spmb-cta>
            <div aria-hidden="true" class="pointer-events-none absolute top-0 left-1/2 size-[36rem] -translate-x-1/2 -translate-y-1/2 rounded-full bg-gold-500/10 blur-3xl"></div>

            <div class="relative mx-auto max-w-4xl px-4 text-center sm:px-6" data-reveal>
                <span class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-gold-500/15 ring-1 ring-gold-500/25">
                    <x-icon name="graduation-cap" class="size-7 text-gold-400" />
                </span>

                <h2 class="mt-6 text-2xl leading-snug font-bold tracking-tight text-balance text-white sm:text-3xl">
                    {{ $page['cta_title'] }}
                </h2>

                <p class="mx-auto mt-3 max-w-2xl text-sm leading-relaxed text-school-100/85 sm:text-base">
                    {{ $page['cta_description'] }}
                </p>

                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    <a
                        href="{{ config('school.contact.whatsapp_link') }}"
                        class="group inline-flex items-center gap-2 rounded-full bg-gold-500 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-gold-500/25 transition-all hover:-translate-y-0.5 hover:bg-gold-600 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gold-400"
                    >
                        <x-icon name="whatsapp" class="size-4" />
                        {{ $page['register_label'] }}
                        <x-icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-0.5" />
                    </a>

                    <a
                        href="{{ route('kontak') }}"
                        class="inline-flex items-center gap-2 rounded-full bg-white/10 px-6 py-2.5 text-sm font-semibold text-white ring-1 ring-white/25 transition-all hover:-translate-y-0.5 hover:bg-white/20 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-white"
                    >
                        <x-icon name="phone" class="size-4" />
                        {{ $page['contact_label'] }}
                    </a>
                </div>
            </div>
        </section>

        <x-contact-strip />
    </main>
</x-layouts.app>