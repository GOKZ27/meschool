{{--
    Slim contact strip. On the home page it sits directly beneath the hero banner
    and above the principal's greeting; on the pages that have no greeting it
    closes <main> instead, so it reads as a footer there.
--}}
<div class="border-t border-school-200 bg-school-50">
    <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-6 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
        <a
            href="https://{{ config('school.domain') }}"
            target="_blank"
            rel="noopener noreferrer"
            class="group flex items-center gap-3 ring-school-focus rounded-sm"
        >
            <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-school-50 text-school-700 ring-1 ring-school-100 transition group-hover:bg-school-100">
                <x-icon name="home" class="size-5" />
            </span>
            <span class="text-sm text-slate-600 sm:text-base">
                Selamat Datang di
                <span class="font-semibold text-school-800">{{ config('school.domain') }}</span>
            </span>
        </a>

        <a
            href="{{ config('school.contact.phone_link') }}"
            class="group flex items-center gap-3 ring-school-focus rounded-sm"
        >
            <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-school-50 text-school-700 ring-1 ring-school-100 transition group-hover:bg-school-100">
                <x-icon name="phone" class="size-5" />
            </span>
            <span class="text-sm text-slate-600 sm:text-base">
                Hubungi kontak pusat informasi :
                <span class="font-semibold text-school-800 tabular-nums">{{ config('school.contact.phone') }}</span>
            </span>
        </a>
    </div>
</div>