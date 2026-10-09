@props([
    // The resolved photo URL, or null when the file has not been uploaded —
    // never a raw filename, the caller runs it through Images::existingUrl().
    'url' => null,
    'alt' => '',
    'aspect' => 'aspect-[16/10]',
    // Draws the play badge over the tile.
    'play' => false,
    // Which glyph the placeholder shows: the category's own icon reads best.
    'icon' => 'document',
])

{{-- One shape for every news thumbnail. A missing upload is a designed brick
     panel rather than a broken <img>, so a half-populated archive still looks
     deliberate — the same contract the hero and the staff photos keep. --}}
<div {{ $attributes->merge(['class' => 'relative isolate overflow-hidden '.$aspect]) }}>
    @if ($url)
        <img
            src="{{ $url }}"
            alt="{{ $alt }}"
            loading="lazy"
            class="absolute inset-0 size-full object-cover transition duration-500 group-hover:scale-[1.04]"
        >
    @else
        <div class="absolute inset-0 bg-brick-green" aria-hidden="true">
            <span class="absolute inset-0 flex items-center justify-center text-white/40">
                <x-icon :name="$icon" class="size-9" />
            </span>
        </div>
    @endif

    @if ($play)
        <span aria-hidden="true" class="absolute inset-0 flex items-center justify-center bg-slate-900/25 transition group-hover:bg-slate-900/40">
            <span class="flex size-12 items-center justify-center rounded-full bg-white/95 text-school-800 shadow-lg shadow-slate-900/20 transition duration-300 group-hover:scale-110">
                <x-icon name="play" class="size-5 translate-x-0.5" />
            </span>
        </span>
    @endif
</div>
