@props([
    // 'row' for the desktop top bar, 'stack' inside the mobile nav drawer.
    'orientation' => 'row',
])

@php
    $contacts = [
        [
            'icon' => 'whatsapp',
            'label' => 'WhatsApp',
            'value' => config('school.contact.whatsapp'),
            'href' => config('school.contact.whatsapp_link'),
            'external' => true,
        ],
        [
            'icon' => 'phone',
            'label' => 'Telepon',
            'value' => config('school.contact.phone'),
            'href' => config('school.contact.phone_link'),
            'external' => false,
        ],
    ];

    $socials = collect(config('school.socials'))
        ->map(fn (array $social, string $key): array => ['icon' => $key] + $social)
        ->values();
@endphp

<div class="{{ $orientation === 'stack' ? 'flex flex-col gap-4' : 'flex items-center gap-6' }}">
    <div class="{{ $orientation === 'stack' ? 'flex flex-col gap-3' : 'flex items-center gap-5' }}">
        @foreach ($contacts as $contact)
            <a
                href="{{ $contact['href'] }}"
                @if ($contact['external'])
                    target="_blank"
                    rel="noopener noreferrer"
                @endif
                class="group flex items-center gap-2.5 text-slate-600 transition hover:text-school-700 ring-school-focus rounded-sm"
            >
                <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-school-50 text-school-700 ring-1 ring-school-100 transition group-hover:bg-school-100">
                    <x-icon :name="$contact['icon']" class="size-4" />
                </span>
                <span class="flex flex-col leading-tight">
                    <span class="text-[0.65rem] font-medium tracking-wide text-slate-400 uppercase">{{ $contact['label'] }}</span>
                    <span class="text-sm font-semibold text-slate-700 tabular-nums">{{ $contact['value'] }}</span>
                </span>
            </a>
        @endforeach
    </div>

    <div class="{{ $orientation === 'stack' ? 'flex items-center gap-2' : 'hidden items-center gap-2 lg:flex' }}">
        <span class="text-xs font-medium text-slate-400">Let's follow:</span>
        <div class="flex items-center gap-1.5">
            @foreach ($socials as $social)
                <a
                    href="{{ $social['url'] }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    title="{{ $social['label'] }}"
                    aria-label="{{ $social['label'] }}"
                    class="flex size-8 items-center justify-center rounded-full text-slate-500 ring-1 ring-slate-200 transition hover:bg-school-50 hover:text-school-700 hover:ring-school-200 ring-school-focus"
                >
                    <x-icon :name="$social['icon']" class="size-4" />
                </a>
            @endforeach
        </div>
    </div>
</div>