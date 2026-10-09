@php
    $contact = config('school.contact');
    $socials = collect(config('school.socials'));
    $name = config('school.name');

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

    // A plain Google Maps embed for the school name keeps the section useful
    // without an API key; it is only an iframe, never a script request.
    $mapEmbed = 'https://maps.google.com/maps?q='.urlencode($name).'&t=&z=15&ie=UTF8&iwloc=&output=embed';
@endphp

{{-- The home page contact block. It sits directly beneath the SPMB photo band,
     so it is white and the panels read as cards on that light ground. --}}
<section id="kontak" class="bg-white py-20 lg:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-7" data-reveal data-reveal-from="left">
                <p class="flex items-center gap-3 text-xs font-semibold tracking-[0.18em] text-school-700 uppercase">
                    <span aria-hidden="true" class="h-px w-10 bg-gold-500"></span>
                    Kontak Kami
                </p>

                <h2 class="mt-5 text-2xl leading-snug font-bold tracking-tight text-balance text-slate-900 sm:text-3xl">
                    Hubungi Sekolah
                </h2>
            </div>

            <div class="lg:col-span-5" data-reveal data-reveal-from="right">
                <p class="text-base leading-relaxed text-slate-600">
                    Hubungi kami melalui salah satu kanal berikut. Tim kami siap membantu pertanyaan
                    seputar pendaftaran, program keahlian, dan informasi akademik lainnya.
                </p>
            </div>
        </div>

        <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4" data-reveal-group>
            @foreach ($channels as $channel)
                <a
                    href="{{ $channel['href'] }}"
                    @if ($channel['external'])
                        target="_blank"
                        rel="noopener noreferrer"
                    @endif
                    class="group flex items-start gap-4 rounded-2xl bg-slate-50 p-6 ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md hover:ring-school-300 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-school-500"
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

        <div class="mt-6 grid gap-5 lg:grid-cols-3" data-reveal-group>
            <div class="lg:col-span-2">
                <div class="flex h-full flex-col rounded-2xl bg-slate-50 p-6 ring-1 ring-slate-200">
                    <h3 class="text-lg font-bold tracking-tight text-slate-800">Lokasi Sekolah</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $contact['address'] }}</p>

                    <div class="mt-5 flex-1 overflow-hidden rounded-xl">
                        <iframe
                            src="{{ $mapEmbed }}"
                            class="h-72 w-full"
                            loading="lazy"
                            title="Peta lokasi {{ $name }}"
                            referrerpolicy="no-referrer-when-downgrade"
                        ></iframe>
                    </div>
                </div>
            </div>

            <div class="flex flex-col rounded-2xl bg-slate-50 p-6 ring-1 ring-slate-200">
                <h3 class="text-lg font-bold tracking-tight text-slate-800">Media Sosial</h3>
                <p class="mt-1 text-sm text-slate-500">Ikuti kanal resmi sekolah.</p>

                <div class="mt-5 flex flex-1 flex-col gap-2.5">
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