@php
    $contact = config('school.contact');
    $socials = collect(config('school.socials'));
    $name = config('school.name');
    $heroPhoto = \App\Support\Images::existingUrl('hero1.jpeg', 'hero');

    $channels = [
        [
            'key' => 'whatsapp',
            'icon' => 'whatsapp',
            'label' => 'WhatsApp',
            'value' => $contact['whatsapp'],
            'href' => $contact['whatsapp_link'],
            'hint' => 'Balasan tercepat pada jam kerja',
            'external' => true,
        ],
        [
            'key' => 'phone',
            'icon' => 'phone',
            'label' => 'Telepon',
            'value' => $contact['phone'],
            'href' => $contact['phone_link'],
            'hint' => 'Pusat informasi sekolah',
            'external' => false,
        ],
        [
            'key' => 'email',
            'icon' => 'mail',
            'label' => 'Email',
            'value' => $contact['email'],
            'href' => 'mailto:'.$contact['email'],
            'hint' => 'Untuk surat resmi dan akademik',
            'external' => false,
        ],
        [
            'key' => 'address',
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

<section id="kontak" class="home-contact" aria-labelledby="home-contact-title">
    <div
        class="home-contact__hero"
        @if ($heroPhoto) style="--home-contact-photo: url('{{ $heroPhoto }}')" @endif
    >
        <div class="home-contact__hero-inner mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="home-contact__intro" data-reveal data-reveal-from="left">
                <p class="home-contact__eyebrow">Kontak Kami</p>
                <h2 id="home-contact-title" class="home-contact__title">Hubungi Sekolah</h2>
                <p class="home-contact__description">
                    Hubungi kami melalui salah satu kanal berikut. Tim kami siap membantu pertanyaan
                    seputar pendaftaran, program keahlian, dan informasi akademik lainnya.
                </p>
            </div>
        </div>
    </div>

    <div class="home-contact__content mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="home-contact__main" data-reveal-group data-contact-layout>
            <div class="home-contact__channels">
                @foreach ($channels as $channel)
                    <a
                        href="{{ $channel['href'] }}"
                        data-contact-channel="{{ $channel['key'] }}"
                        @if ($channel['external'])
                            target="_blank"
                            rel="noopener noreferrer"
                        @endif
                    >
                        <span class="home-contact__channel-icon" aria-hidden="true">
                            <x-icon :name="$channel['icon']" class="size-5" />
                        </span>
                        <span class="home-contact__channel-copy">
                            <span class="home-contact__channel-label">{{ $channel['label'] }}</span>
                            <span class="home-contact__channel-value">{{ $channel['value'] }}</span>
                            <span class="home-contact__channel-hint">{{ $channel['hint'] }}</span>
                        </span>
                        <x-icon name="arrow-right" class="home-contact__channel-arrow size-4" />
                    </a>
                @endforeach
            </div>

            <article class="home-contact__location" data-contact-map>
                <div class="home-contact__location-heading">
                    <span class="home-contact__location-icon" aria-hidden="true">
                        <x-icon name="map-pin" class="size-5" />
                    </span>
                    <div>
                        <h3>Lokasi Sekolah</h3>
                        <p>{{ $contact['address'] }}</p>
                    </div>
                </div>

                <div class="home-contact__map">
                    <iframe
                        src="{{ $mapEmbed }}"
                        loading="lazy"
                        title="Peta lokasi {{ $name }}"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                </div>

                <a class="home-contact__map-link" href="{{ $contact['maps_url'] }}" target="_blank" rel="noopener noreferrer">
                    <x-icon name="map-pin" class="size-4" />
                    <span>Peta lokasi {{ $name }}</span>
                    <x-icon name="arrow-right" class="size-4" />
                </a>
            </article>
        </div>

        <div class="home-contact__socials" data-contact-socials>
            <div class="home-contact__social-intro">
                <h3>Media Sosial</h3>
                <p>Ikuti kanal resmi sekolah.</p>
                <span aria-hidden="true"></span>
            </div>

            @foreach ($socials as $key => $social)
                <a
                    class="home-contact__social"
                    href="{{ $social['url'] }}"
                    data-contact-social
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <span class="home-contact__social-icon home-contact__social-icon--{{ $key }}" aria-hidden="true">
                        <x-icon :name="$key" class="size-5" />
                    </span>
                    <span class="home-contact__social-copy">
                        <span>{{ $social['label'] }}</span>
                        <small>@smkislamiyahciputat</small>
                    </span>
                </a>
            @endforeach

            <p class="home-contact__motto">Bersama Membangun Masa Depan yang Lebih Baik</p>
        </div>
    </div>
</section>