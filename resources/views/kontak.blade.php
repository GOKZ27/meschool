@php
    $contact = config('school.contact');
    $socials = collect(config('school.socials'));

    $channels = [
        [
            'icon' => 'whatsapp',
            'label' => 'WhatsApp',
            'value' => $contact['whatsapp'],
            'href' => $contact['whatsapp_link'],
            'hint' => 'Balasan tercepat pada jam kerja',
            'external' => true,
        ],
        [
            'icon' => 'phone',
            'label' => 'Telepon',
            'value' => $contact['phone'],
            'href' => $contact['phone_link'],
            'hint' => 'Pusat informasi sekolah',
            'external' => false,
        ],
        [
            'icon' => 'mail',
            'label' => 'Email',
            'value' => $contact['email'],
            'href' => 'mailto:'.$contact['email'],
            'hint' => 'Untuk surat resmi dan akademik',
            'external' => false,
        ],
        [
            'icon' => 'map-pin',
            'label' => 'Alamat',
            'value' => $contact['address'],
            'href' => $contact['maps_url'],
            'hint' => 'Kunjungi sekolah pada jam kerja',
            'external' => true,
        ],
    ];
@endphp

<x-layouts.app
    :title="'Kontak — '.config('school.name')"
    :description="'Hubungi '.config('school.name').' — alamat, telepon, email, dan media sosial resmi sekolah di '.config('school.contact.address').'.'"
>
    <x-header-top />
    <x-navbar />

    <main>
        <section class="bg-brick-green">
            <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8 lg:py-16">
                <p class="text-xs font-semibold tracking-widest text-school-200/80 uppercase">Informasi</p>
                <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-white sm:text-3xl">Kontak</h1>
                <p class="mt-3 max-w-2xl text-sm leading-relaxed text-school-100/85 sm:text-base">
                    Hubungi kami melalui salah satu kanal berikut. Tim kami siap membantu pertanyaan
                    seputar pendaftaran, program keahlian, dan informasi akademik lainnya.
                </p>
            </div>
        </section>

        <section class="bg-slate-50">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
                <div class="grid gap-5 sm:grid-cols-2">
                    @foreach ($channels as $channel)
                        <a
                            href="{{ $channel['href'] }}"
                            @if ($channel['external'])
                                target="_blank"
                                rel="noopener noreferrer"
                            @endif
                            class="group flex items-start gap-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md hover:ring-school-300 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-school-500"
                        >
                            <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-school-50 text-school-700 ring-1 ring-school-100 transition group-hover:bg-school-700 group-hover:text-white group-hover:ring-school-700">
                                <x-icon :name="$channel['icon']" class="size-5" />
                            </span>

                            <span class="min-w-0">
                                <span class="block text-xs font-semibold tracking-wide text-slate-400 uppercase">{{ $channel['label'] }}</span>
                                <span class="mt-1 block text-base font-semibold break-words text-slate-800">{{ $channel['value'] }}</span>
                                <span class="mt-1 block text-sm text-slate-500">{{ $channel['hint'] }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>

                <div class="mt-6 grid gap-5 lg:grid-cols-3">
                    <div class="lg:col-span-2">
                        <div class="flex h-full flex-col rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                            <h2 class="text-lg font-bold tracking-tight text-slate-800">Lokasi Sekolah</h2>
                            <p class="mt-1 text-sm text-slate-500">{{ $contact['address'] }}</p>

                            <div class="mt-5 flex flex-1 items-center justify-center rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 p-6 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <x-icon name="map-pin" class="size-7 text-slate-300" />
                                    <p class="max-w-xs text-xs leading-relaxed text-slate-400">
                                        Peta akan ditampilkan di area ini.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <h2 class="text-lg font-bold tracking-tight text-slate-800">Media Sosial</h2>
                        <p class="mt-1 text-sm text-slate-500">Ikuti kanal resmi sekolah.</p>

                        <div class="mt-5 flex flex-col gap-2.5">
                            @foreach ($socials as $key => $social)
                                <a
                                    href="{{ $social['url'] }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-school-50 hover:text-school-800 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-school-500"
                                >
                                    <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500 ring-1 ring-slate-200 transition group-hover:bg-white group-hover:text-school-700 group-hover:ring-school-200">
                                        <x-icon :name="$key" class="size-4" />
                                    </span>
                                    <span>{{ $social['label'] }}</span>
                                    <x-icon name="arrow-right" class="ml-auto size-4 text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-school-500" />
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <x-contact-strip />
    </main>
</x-layouts.app>