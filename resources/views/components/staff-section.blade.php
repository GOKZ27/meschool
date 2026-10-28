@php
    use App\Support\Images;
    use Illuminate\Support\Str;

    $staff = config('school.staff');
    $items = array_values(array_filter((array) ($staff['items'] ?? [])));
    $accents = [
        'var(--color-school-700)',
        'var(--color-school-800)',
        'var(--color-school-600)',
        'var(--color-school-900)',
        'var(--color-school-700)',
        'var(--color-school-800)',
        'var(--color-school-600)',
        'var(--color-school-900)',
    ];
    $people = [];

    foreach ($items as $index => $item) {
        $photo = (string) ($item['photo'] ?? '');
        $name = (string) ($item['name'] ?? '');

        $people[] = [
            'name' => $name,
            'position' => (string) ($item['position'] ?? ''),
            'description' => (string) ($item['description'] ?? ''),
            'expertise' => array_values(array_filter((array) ($item['expertise'] ?? []))),
            'photo' => $photo === '' ? null : (Images::existingUrl($photo, 'guru') ?? Images::existingUrl($photo)),
            'initials' => Str::upper(Str::initials(Str::before($name, ','))),
            'accent' => $accents[$index % count($accents)],
        ];
    }
@endphp

<section id="tenaga-kependidikan" class="tk-section" aria-labelledby="tk-title">
    <div class="tk-shell">
        <header class="tk-heading" data-reveal>
            <div>
                <p class="tk-eyebrow">
                    <span aria-hidden="true"></span>
                    {{ $staff['eyebrow'] }}
                </p>
                <h2 id="tk-title">Guru Profesional</h2>
                <p class="tk-heading-intro">
                    Tenaga pendidik di {{ config('school.name') }} adalah para guru yang berkompeten,
                    berpengalaman, dan berdedikasi tinggi dalam mendidik serta membimbing peserta didik
                    menuju masa depan yang lebih baik.
                </p>
            </div>
        </header>

        <ul
            class="tk-list"
            data-staff-rail
            data-reveal-group
            data-reveal-slow
            aria-label="Daftar tenaga pendidik dan kependidikan"
        >
            @foreach ($people as $index => $person)
                <li
                    class="tk-card"
                    data-staff-row
                    style="--tk-accent: {{ $person['accent'] }}"
                >
                    <button
                        class="tk-trigger"
                        type="button"
                        data-staff-open
                        data-staff-index="{{ $index }}"
                        aria-haspopup="dialog"
                        aria-controls="staff-profile-dialog"
                        aria-label="Lihat profil {{ $person['name'] }}, {{ $person['position'] }}"
                    >
                        <span class="tk-card-photo">
                            @if ($person['photo'])
                                <img
                                    src="{{ $person['photo'] }}"
                                    alt=""
                                    width="640"
                                    height="800"
                                    loading="lazy"
                                />
                            @else
                                <span class="tk-card-monogram" aria-hidden="true">{{ $person['initials'] }}</span>
                            @endif
                        </span>

                        <span class="tk-card-copy">
                            <span class="tk-index" aria-hidden="true">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="tk-name" role="heading" aria-level="3">{{ $person['name'] }}</span>
                            <span class="tk-position">{{ $person['position'] }}</span>
                            @if (! empty($person['expertise']))
                                <span class="tk-card-expertise">
                                    <span class="tk-expertise-mark" aria-hidden="true"></span>
                                    {{ $person['expertise'][0] }}
                                </span>
                            @endif
                            <span class="tk-card-open">Lihat profil <span class="tk-arrow" aria-hidden="true"></span></span>
                        </span>
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    <aside class="tk-preview" data-staff-preview aria-hidden="true">
        <div class="tk-preview-photo" data-staff-preview-photo></div>
        <div class="tk-preview-copy">
            <p class="tk-preview-position" data-staff-preview-position></p>
            <h3 data-staff-preview-name></h3>
            <p class="tk-preview-description" data-staff-preview-description></p>
            <span class="tk-preview-hint">Buka profil <span class="tk-arrow" aria-hidden="true"></span></span>
        </div>
    </aside>

    <dialog
        class="tk-modal"
        id="staff-profile-dialog"
        data-staff-modal
        aria-labelledby="staff-modal-name"
        aria-describedby="staff-modal-description"
    >
        <article class="tk-modal-panel" data-staff-panel>
            <div class="tk-modal-portrait" data-staff-modal-photo></div>

            <div class="tk-modal-copy">
                <div class="tk-modal-topline">
                    <p class="tk-eyebrow"><span aria-hidden="true"></span>Profil tenaga kependidikan</p>
                    <button
                        class="tk-close"
                        type="button"
                        data-staff-close
                        aria-label="Tutup profil"
                    >
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <p class="tk-modal-position" data-staff-modal-position></p>
                <h3 id="staff-modal-name" data-staff-modal-name></h3>
                <p class="tk-modal-description" id="staff-modal-description" data-staff-modal-description></p>

                <div class="tk-expertise-wrap">
                    <p class="tk-expertise-label">Bidang keahlian</p>
                    <ul class="tk-expertise" data-staff-modal-expertise></ul>
                </div>

                <nav class="tk-modal-navigation" aria-label="Navigasi profil">
                    <button type="button" data-staff-prev aria-label="Profil sebelumnya">
                        <span aria-hidden="true">←</span> Sebelumnya
                    </button>
                    <span data-staff-count aria-live="polite"></span>
                    <button type="button" data-staff-next aria-label="Profil berikutnya">
                        Berikutnya <span aria-hidden="true">→</span>
                    </button>
                </nav>
            </div>
        </article>
    </dialog>

    <script type="application/json" data-staff-data>@json($people)</script>
</section>
