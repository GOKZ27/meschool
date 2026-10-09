@props([
    'job',
    'size' => 'sm', // 'sm' on cards, 'lg' on the detail page header
])

@php
    $shape = $size === 'lg' ? 'size-16 rounded-2xl p-3 text-lg' : 'size-11 rounded-xl p-2 text-sm';
@endphp

@if ($job['logo_url'])
    <img
        src="{{ $job['logo_url'] }}"
        alt="Logo {{ $job['company'] }}"
        class="{{ $shape }} shrink-0 bg-white object-contain ring-1 ring-slate-200"
    >
@else
    {{-- A missing logo never leaves a broken <img>: the company monogram
         stands in, the same way the staff cards do. --}}
    <span
        {{ $attributes->merge(['class' => $shape.' flex shrink-0 items-center justify-center bg-school-50 font-bold tracking-wide text-school-700 ring-1 ring-school-500/25']) }}
        aria-hidden="true"
    >
        {{ $job['logo_mark'] }}
    </span>
@endif