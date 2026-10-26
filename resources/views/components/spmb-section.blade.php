@php
    use App\Support\Images;

    $copy = config('school.spmb');

    // A pair of photos, both optional: the building shot behind the band and the
    // student shot in the right-hand panel. Each resolves through existingUrl(),
    // so an upload that has not landed yet falls back to the gradient/monogram
    // below instead of a broken layer.
    $bgPath = (string) ($copy['bg_photo'] ?? '');
    $bg = $bgPath !== '' ? Images::existingUrl($bgPath) : null;

    $photoPath = (string) ($copy['photo'] ?? '');
    $photo = $photoPath !== '' ? Images::existingUrl($photoPath) : null;

    $benefits = $copy['benefits'] ?? [];
    $benefitItems = $benefits['items'] ?? [];

    // Registration goes to the school's WhatsApp channel (the same destination
    // the /spmb page uses), while the second call-to-action opens the
    // information page. A dedicated registration URL can be configured later.
    $registerUrl = $copy['register_url'] ?? config('school.contact.whatsapp_link');
@endphp

{{-- The SPMB admission block: a deep-green hero with the copy on the left and
     the student photo on the right, its bottom edge curving into the benefits
     band that follows. --}}
<section
    id="spmb"
    aria-labelledby="spmb-title"
    class="spmb-hero relative isolate min-h-[500px] overflow-hidden bg-school-950"
>
    @if ($bg)
        <div
            aria-hidden="true"
            data-spmb-bg
            class="spmb-hero__bg"
            style="background-image:url('{{ $bg }}')"
        ></div>
    @else
        <div aria-hidden="true" class="absolute inset-0 -z-20 bg-gradient-to-br from-school-700 via-school-800 to-school-950"></div>
    @endif

    <div
        aria-hidden="true"
        data-spmb-overlay
        class="absolute inset-0 -z-10 {{ $copy['overlay'] ?? 'bg-gradient-to-r from-school-950/95 via-school-950/80 to-school-950/40' }}"
    ></div>

    <div class="spmb-hero__inner">
        <div class="spmb-hero__grid">
            <div class="spmb-hero__copy" data-reveal-group>
                <p class="spmb-hero__badge">
                    <x-icon name="graduation-cap" class="spmb-hero__badge-icon" />
                    <span>{{ $copy['eyebrow'] ?? 'SPMB' }}</span>
                </p>

                <h2 id="spmb-title" class="spmb-hero__title">
                    {{ $copy['title'] }}
                </h2>

                <p class="spmb-hero__school">
                    {{ $copy['school_name'] ?? config('school.name') }}
                </p>

                <p class="spmb-hero__year">
                    <x-icon name="calendar" class="spmb-hero__year-icon" />
                    <span>{{ $copy['subtitle'] }}</span>
                </p>

                <span aria-hidden="true" class="spmb-hero__rule"></span>

                <p class="spmb-hero__desc">
                    {{ $copy['description'] }}
                </p>

                <div class="spmb-hero__actions">
                    <a
                        href="{{ $registerUrl }}"
                        class="spmb-hero__cta spmb-hero__cta--primary"
                    >
                        <x-icon name="graduation-cap" class="spmb-hero__cta-icon" />
                        <span>{{ $copy['button_label'] }}</span>
                        <x-icon name="arrow-right" class="spmb-hero__cta-icon spmb-hero__cta-arrow" />
                    </a>

                    <a
                        href="{{ route('spmb') }}"
                        class="spmb-hero__cta spmb-hero__cta--ghost"
                    >
                        <x-icon name="info" class="spmb-hero__cta-icon" />
                        <span>{{ $copy['link_label'] }}</span>
                    </a>
                </div>
            </div>

            <figure class="spmb-hero__visual" data-reveal data-reveal-from="right">
                @if ($photo)
                    <img
                        src="{{ $photo }}"
                        alt="{{ $copy['photo_alt'] ?? '' }}"
                        width="1200"
                        height="800"
                        loading="lazy"
                        decoding="async"
                        class="spmb-hero__photo"
                    />
                @else
                    <div class="spmb-hero__photo spmb-hero__photo--empty" aria-hidden="true">
                        <x-icon name="graduation-cap" class="size-12" />
                    </div>
                @endif
            </figure>
        </div>
    </div>

    {{-- The white curve that hands the dark hero over to the benefits band. --}}
    <div aria-hidden="true" class="spmb-hero__wave">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,52 C240,110 480,10 720,38 C960,66 1200,104 1440,56 L1440,120 L0,120 Z"></path>
        </svg>
    </div>
</section>

{{-- Five reasons to choose the school, sat on the pale blue-white ground the
     hero curves into. --}}
<section
    data-spmb-benefits
    aria-labelledby="spmb-benefits-title"
    class="spmb-benefits"
>
    <div class="spmb-benefits__inner">
        <div class="spmb-benefits__intro" data-reveal data-reveal-from="left">
            <p class="spmb-benefits__label">
                {{ $benefits['label'] ?? '' }}
            </p>

            <h2 id="spmb-benefits-title" class="spmb-benefits__title">
                {{ $benefits['title'] ?? '' }}
            </h2>

            <p class="spmb-benefits__desc">
                {{ $benefits['description'] ?? '' }}
            </p>
        </div>

        <ul class="spmb-benefits__grid" data-reveal-group data-reveal-from="right" data-reveal-slow>
            @foreach ($benefitItems as $item)
                <li class="spmb-benefit-card" data-spmb-benefit>
                    <span class="spmb-benefit-card__icon" aria-hidden="true">
                        <x-icon :name="$item['icon'] ?? 'check-badge'" class="size-5" />
                    </span>

                    <div class="spmb-benefit-card__body">
                        <h3 class="spmb-benefit-card__title">{{ $item['title'] }}</h3>
                        <p class="spmb-benefit-card__desc">{{ $item['description'] }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</section>
