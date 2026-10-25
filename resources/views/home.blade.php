<x-layouts.app>
    <x-header-top />
    <x-navbar />
    <main class="home-main">
        <x-hero-section />
        <x-contact-strip />
        <x-principal-greeting />
        <x-programs-section />
        <x-staff-section />
        <x-news-preview :news="$news" />
        <x-bkk-section :jobs="$bkk" />
        <x-spmb-section />
        <x-contact-section />
    </main>
</x-layouts.app>