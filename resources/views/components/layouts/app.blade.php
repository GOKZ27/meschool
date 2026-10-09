@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{ config('school.description') }}">

        {{-- A page may pass `title="…"`, or a view may still `@section('title')`.
             Either way the school name is the last resort. --}}
        <title>@yield('title', $title ?: config('school.name'))</title>

        {{-- Set before first paint so scroll-reveal copy is never painted
             visible and then hidden again by app.css. --}}
        <script>document.documentElement.classList.add('has-js');</script>

        @fonts

        {{-- Guarded so the page still renders before `npm run build` has ever run. --}}
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-white font-sans text-slate-800 antialiased">
        {{ $slot }}
        <x-footer />
    </body>
</html>