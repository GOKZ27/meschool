@props([
    // Height-driven by default: the mark is portrait, so callers pin `h-*` and
    // let the width follow the aspect ratio rather than using a square box.
    'class' => 'h-12 w-auto',
    // 'light' draws a white plate behind the mark. The real logo is dark navy on
    // a transparent background, so it disappears on the dark navbar without one.
    // Leave null on light backgrounds (the white header) where it needs no help.
    'surface' => null,
])

@php
    use App\Support\Images;

    // Resolve through the helper so a configured-but-not-yet-uploaded file falls
    // back to the typographic placeholder instead of rendering a broken image.
    $url = Images::existingUrl((string) config('school.logo.path'));

    // Every class must be emitted as ONE attribute. Emitting @class(...) and
    // $attributes->merge(['class' => ...]) separately writes two class="..."
    // attributes, the second of which the browser discards - which silently
    // dropped the caller's h-*/w-auto sizing and let the mark render at its
    // full intrinsic 219x274 and blow the bar out.
    $classes = ['object-contain'];

    if ($surface === 'light') {
        $classes[] = 'rounded-md bg-white p-0.5';
    }
@endphp

@if ($url)
    <img
        src="{{ $url }}"
        alt="{{ config('school.logo.alt') }}"
        width="219"
        height="274"
        {{ $attributes->merge(['class' => implode(' ', $classes).' '.$class]) }}
    >
@else
    <span
        {{ $attributes->merge([
            'class' => $class.' inline-flex shrink-0 aspect-square items-center justify-center rounded-xl '
                .'bg-gradient-to-br from-school-500 to-school-800 text-white '
                .'shadow-sm ring-1 ring-school-900/10',
        ]) }}
        role="img"
        aria-label="{{ config('school.logo.alt') }}"
    >
        <x-icon name="graduation-cap" class="size-1/2" />
    </span>
@endif