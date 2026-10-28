@php
    $contact = config('school.contact');
    $socials = config('school.socials');
    $name = config('school.name');
    $tagline = config('school.tagline');

    $menu = [
        ['label' => 'Beranda', 'name' => 'home'],
        ['label' => 'Tenaga Kependidikan', 'name' => 'tenaga-kependidikan'],
        ['label' => 'Berita', 'name' => 'berita'],
        ['label' => 'BKK', 'name' => 'bkk'],
        ['label' => 'SPMB', 'name' => 'spmb'],
        ['label' => 'Kontak', 'name' => 'kontak'],
    ];

    $programs = [
        ['label' => 'Kuliner', 'name' => 'program.kuliner'],
        ['label' => 'DKV', 'name' => 'program.dkv'],
        ['label' => 'TKJ', 'name' => 'program.tkj'],
        ['label' => 'AK', 'name' => 'program.ak'],
        ['label' => 'MP', 'name' => 'program.mp'],
        ['label' => 'BD', 'name' => 'program.bd'],
    ];
@endphp

{{-- The single site-wide footer. It lives in the layout, after </main>, so it is
     never a descendant of <main> and it closes every page the same way. --}}
<footer class="bg-school-950 text-school-100">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 ring-school-focus rounded-sm">
                    <x-logo surface="light" class="h-10 w-auto shrink-0" />
                    <span class="text-sm font-bold tracking-wide text-white">{{ $name }}</span>
                </a>

                <p class="mt-4 max-w-sm text-sm leading-relaxed text-school-200/80">{{ $tagline }}</p>
                <p class="mt-4 max-w-sm text-sm leading-relaxed text-school-200/80">{{ config('school.description') }}</p>

                <div class="mt-6 flex items-center gap-2">
                    @foreach ($socials as $key => $social)
                        <a
                            href="{{ $social['url'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="{{ $social['label'] }} {{ $name }}"
                            class="flex size-10 shrink-0 items-center justify-center rounded-full bg-white/10 text-white ring-1 ring-white/15 transition hover:bg-gold-500 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gold-400"
                        >
                            <x-icon :name="$key" class="size-4" />
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="lg:col-span-2">
                <h3 class="text-xs font-semibold tracking-[0.18em] text-school-300 uppercase">Menu</h3>
                <ul class="mt-5 space-y-3">
                    @foreach ($menu as $item)
                        <li>
                            <a href="{{ route($item['name']) }}" class="text-sm text-school-100/80 transition hover:text-white">
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-3">
                <h3 class="text-xs font-semibold tracking-[0.18em] text-school-300 uppercase">Program Keahlian</h3>
                <ul class="mt-5 grid grid-cols-2 gap-x-6 gap-y-3">
                    @foreach ($programs as $item)
                        <li>
                            <a href="{{ route($item['name']) }}" class="text-sm text-school-100/80 transition hover:text-white">
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-3">
                <h3 class="text-xs font-semibold tracking-[0.18em] text-school-300 uppercase">Kontak</h3>
                <ul class="mt-5 space-y-3">
                    <li>
                        <a href="{{ $contact['maps_url'] }}" target="_blank" rel="noopener noreferrer" class="group flex items-start gap-3 text-sm text-school-100/80 transition hover:text-white">
                            <x-icon name="map-pin" class="mt-0.5 size-4 shrink-0 text-gold-400" />
                            <span>{{ $contact['address'] }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $contact['phone_link'] }}" class="group flex items-center gap-3 text-sm text-school-100/80 transition hover:text-white">
                            <x-icon name="phone" class="size-4 shrink-0 text-gold-400" />
                            <span class="tabular-nums">{{ $contact['phone'] }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="mailto:{{ $contact['email'] }}" class="group flex items-center gap-3 text-sm text-school-100/80 transition hover:text-white">
                            <x-icon name="mail" class="size-4 shrink-0 text-gold-400" />
                            <span>{{ $contact['email'] }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $contact['whatsapp_link'] }}" target="_blank" rel="noopener noreferrer" class="group flex items-center gap-3 text-sm text-school-100/80 transition hover:text-white">
                            <x-icon name="whatsapp" class="size-4 shrink-0 text-gold-400" />
                            <span>{{ $contact['whatsapp'] }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="mt-12 flex flex-col gap-2 border-t border-white/10 pt-6 text-center text-xs text-school-200/60 sm:flex-row sm:items-center sm:justify-between sm:text-left">
            <p>&copy; {{ date('Y') }} {{ $name }}. Semua hak cipta dilindungi.</p>
            <p class="italic">{{ $tagline }}</p>
        </div>
    </div>
</footer>