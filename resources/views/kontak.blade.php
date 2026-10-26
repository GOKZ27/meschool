@php
    $contact = config('school.contact');
    $schoolName = config('school.name');

    // The building photo ships with the repo; when it is replaced with a missing
    // file the hero keeps the deep-green ground instead of showing a broken image.
    $heroPhoto = \App\Support\Images::existingUrl('hero/hero1.jpeg');

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
            'compact' => true,
        ],
        [
            'icon' => 'map-pin',
            'label' => 'Alamat',
            'value' => $contact['address'],
            'href' => $contact['maps_url'],
            'hint' => 'Kunjungi sekolah pada jam kerja',
            'external' => true,
            'address' => true,
        ],
    ];

    // Handles are page copy; the URLs always come from config so the footer and
    // this page can never point at different accounts.
    $socialLinks = [
        ['icon' => 'instagram', 'label' => 'Instagram', 'handle' => '@smkislamiyahciputat'],
        ['icon' => 'facebook', 'label' => 'Facebook', 'handle' => 'smkislamiyahciputat'],
        ['icon' => 'youtube', 'label' => 'YouTube', 'handle' => '@smkislamiyahciputat'],
    ];

    foreach ($socialLinks as $index => $social) {
        $socialLinks[$index]['url'] = config('school.socials.'.$social['icon'].'.url', '#');
    }

    // The reference breaks the address after the street, before the regency.
    $addressLines = preg_split('/,\s*/', (string) $contact['address'], 2);

    $mapEmbed = 'https://maps.google.com/maps?q='.urlencode('SMK ISLAMIYAH CIPUTAT').'&t=&z=15&ie=UTF8&iwloc=&output=embed';
@endphp

<x-layouts.app
    :title="'Kontak — '.$schoolName"
    :description="'Hubungi '.$schoolName.' — alamat, telepon, email, dan media sosial resmi sekolah di '.$contact['address'].'.'"
>
    <x-header-top />
    <x-navbar />

    <main class="kontak-page">
        {{-- Hero: the campus photo with a green scrim on the copy side and the
             wide white curve that hands it over to the pale ground below. --}}
        <section class="kontak-hero" aria-labelledby="kontak-title">
            <div
                class="kontak-hero__photo"
                aria-hidden="true"
                @if ($heroPhoto) style="background-image: url('{{ $heroPhoto }}')" @endif
            ></div>
            <div class="kontak-hero__veil" aria-hidden="true"></div>

            <div class="kontak-hero__inner mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="kontak-hero__copy">
                    <p class="kontak-hero__badge">
                        <x-icon name="phone" class="size-3.5" />
                        <span>Kontak Kami</span>
                    </p>

                    <h1 id="kontak-title" class="kontak-hero__title">
                        <span>Hubungi</span>
                        <span class="kontak-hero__title-accent">Sekolah</span>
                    </h1>

                    <span class="kontak-hero__rule" aria-hidden="true"></span>

                    <p class="kontak-hero__desc">
                        Hubungi kami melalui salah satu kanal berikut. Tim kami siap membantu pertanyaan
                        seputar pendaftaran, program keahlian, dan informasi akademik lainnya.
                    </p>
                </div>
            </div>

            <div class="kontak-hero__curve" aria-hidden="true">
                <svg viewBox="0 0 1440 72" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 0C420 66 1020 66 1440 0V72H0Z" />
                </svg>
            </div>
        </section>

        {{-- The four channels. Every card is one anchor, so the whole tile is a
             tap target on a phone and the arrow needs no script. --}}
        <section class="kontak-cards" aria-label="Kanal kontak sekolah">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <ul class="kontak-cards__grid">
                    @foreach ($channels as $channel)
                        <li>
                            <a
                                href="{{ $channel['href'] }}"
                                class="kontak-card"
                                @if ($channel['external'])
                                    target="_blank"
                                    rel="noopener noreferrer"
                                @endif
                            >
                                <span class="kontak-card__icon" aria-hidden="true">
                                    <span class="kontak-card__icon-inner">
                                        <x-icon :name="$channel['icon']" class="size-5" />
                                    </span>
                                </span>

                                <span class="kontak-card__body">
                                    <span class="kontak-card__title">{{ $channel['label'] }}</span>
                                    <span
                                        @class([
                                            'kontak-card__value',
                                            'kontak-card__value--email' => $channel['compact'] ?? false,
                                            'kontak-card__value--address' => $channel['address'] ?? false,
                                        ])
                                    >{{ $channel['value'] }}</span>
                                    <span class="kontak-card__hint">{{ $channel['hint'] }}</span>
                                </span>

                                <span class="kontak-card__arrow" aria-hidden="true">
                                    <x-icon name="arrow-right" class="size-4" />
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        {{-- Location and social channels, two panels on one ground. --}}
        <section class="kontak-panels" aria-label="Lokasi sekolah dan media sosial">
            <div class="kontak-panels__decor" aria-hidden="true">
                <svg class="kontak-panels__decor-wave" viewBox="0 0 600 400" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 320C90 250 180 340 280 300S470 210 600 270V400H0Z" fill="currentColor" />
                </svg>
                <svg
                    class="kontak-panels__decor-lines"
                    viewBox="0 0 600 400"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path d="M120 400 520 20" />
                    <path d="M215 400 570 90" />
                    <path d="M310 400 600 155" />
                </svg>
            </div>

            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="kontak-panels__grid">
                    <article class="kontak-panel">
                        <p class="kontak-panel__eyebrow">Lokasi Sekolah</p>
                        <h2 class="kontak-panel__title">
                            {{ $addressLines[0] ?? $contact['address'] }},<br />
                            {{ $addressLines[1] ?? '' }}
                        </h2>

                        <div class="kontak-map">
                            <iframe
                                src="{{ $mapEmbed }}"
                                title="Peta lokasi {{ $schoolName }}"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                allowfullscreen
                            ></iframe>
                        </div>

                        <a
                            class="kontak-map-link"
                            href="{{ $contact['maps_url'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <x-icon name="map-pin" class="size-4" />
                            <span>Peta lokasi {{ $schoolName }}</span>
                            <x-icon name="arrow-right" class="size-4" />
                        </a>
                    </article>

                    <aside class="kontak-panel" aria-labelledby="kontak-social-title">
                        <p class="kontak-panel__eyebrow">Media Sosial</p>
                        <h2 id="kontak-social-title" class="kontak-panel__title">Ikuti kanal resmi sekolah.</h2>

                        <ul class="kontak-social">
                            @foreach ($socialLinks as $social)
                                <li>
                                    <a
                                        class="kontak-social__card"
                                        href="{{ $social['url'] }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        <span class="kontak-social__icon" aria-hidden="true">
                                            <x-icon :name="$social['icon']" class="size-5" />
                                        </span>
                                        <span class="kontak-social__body">
                                            <span class="kontak-social__name">{{ $social['label'] }}</span>
                                            <span class="kontak-social__handle">{{ $social['handle'] }}</span>
                                        </span>
                                        <x-icon name="arrow-right" class="kontak-social__arrow size-4" />
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </aside>
                </div>
            </div>
        </section>

        <x-contact-strip />
    </main>
</x-layouts.app>
