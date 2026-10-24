<x-layouts.app :title="$title" :description="$description" robots="noindex, follow">
    <x-header-top />
    <x-navbar />

    <main>
        <section class="bg-brick-green">
            <div class="mx-auto max-w-3xl px-4 py-20 text-center sm:px-6 lg:px-8">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3.5 py-1.5 text-xs font-semibold tracking-wide text-school-100 uppercase ring-1 ring-white/15">
                    <x-icon name="sparkles" class="size-4 text-gold-400" />
                    Segera hadir
                </span>

                <h1 class="mt-5 text-2xl font-extrabold tracking-tight text-white sm:text-3xl">{{ $title }}</h1>
                <p class="mt-3 text-sm leading-relaxed text-school-100/85 sm:text-base">{{ $description }}</p>

                <a
                    href="{{ route('home') }}"
                    class="mt-8 inline-flex items-center gap-2 rounded-full bg-gold-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-gold-600"
                >
                    <x-icon name="arrow-right" class="size-4 rotate-180" />
                    Kembali ke Beranda
                </a>
            </div>
        </section>

        <x-contact-strip />
    </main>
</x-layouts.app>