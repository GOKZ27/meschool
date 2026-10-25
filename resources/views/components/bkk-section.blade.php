@props(['jobs'])

@php
    $copy = config('school.bkk.home');
    $title = \Illuminate\Support\Str::beforeLast($copy['title'], ' (BKK)');
    $categories = $jobs
        ->map(fn (array $job) => ['key' => $job['category'], 'label' => $job['category_label']])
        ->unique('key')
        ->values();
@endphp

<section id="bkk" class="bkk-section" data-bkk-section aria-labelledby="bkk-title">
    <div class="bkk-shell">
        <header class="bkk-header" data-bkk-reveal>
            <p class="bkk-eyebrow"><span aria-hidden="true"></span>{{ $copy['eyebrow'] }}</p>
            <div class="bkk-header-grid">
                <h2 id="bkk-title" aria-label="{{ $copy['title'] }}">
                    {{ $title }} <span>(BKK)</span>
                </h2>
                <p class="bkk-intro">{{ $copy['intro'] }}</p>
            </div>
        </header>

        @if ($jobs->isNotEmpty())
            <nav class="bkk-filters" aria-label="Filter lowongan berdasarkan kategori" data-bkk-filters>
                <button type="button" data-bkk-filter="all" aria-pressed="true">Semua</button>
                @foreach ($categories as $category)
                    <button
                        type="button"
                        data-bkk-filter="{{ $category['key'] }}"
                        aria-pressed="false"
                    >
                        {{ $category['label'] }}
                    </button>
                @endforeach
            </nav>

            <ol class="bkk-job-list" id="bkk-job-list">
                @foreach ($jobs as $job)
                    @php
                        $photoPath = (string) ($job['photo'] ?? '');
                        $photo = $photoPath !== ''
                            ? (\App\Support\Images::existingUrl($photoPath, 'bkk') ?? \App\Support\Images::existingUrl($photoPath))
                            : null;
                    @endphp

                    <li
                        class="bkk-job-item"
                        data-bkk-job
                        data-category="{{ $job['category'] }}"
                        data-bkk-side="{{ $loop->odd ? 'odd' : 'even' }}"
                        data-bkk-reveal
                    >
                        <article>
                            <a
                                href="{{ route('bkk.show', $job['slug']) }}"
                                class="bkk-job-link"
                                aria-label="Detail lowongan {{ $job['position'] }} di {{ $job['company'] }}"
                            >
                                <figure class="bkk-photo-frame">
                                    <span class="bkk-photo-window">
                                        @if ($photo)
                                            <img
                                                src="{{ $photo }}"
                                                alt="Foto lowongan {{ $job['position'] }}"
                                                width="1200"
                                                height="900"
                                                @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif
                                                class="bkk-photo-image"
                                            >
                                        @else
                                            <span class="bkk-photo-placeholder">Foto lowongan</span>
                                        @endif
                                    </span>

                                </figure>

                                <div class="bkk-copy">
                                    <p class="bkk-category">{{ $job['category_label'] }} · {{ $job['type'] }}</p>
                                    <h3>{{ $job['position'] }}</h3>
                                    <p class="bkk-company">{{ $job['company'] }}</p>

                                    <span class="bkk-rule" aria-hidden="true"></span>

                                    <dl class="bkk-meta">
                                        <div>
                                            <dt>Lokasi</dt>
                                            <dd>{{ $job['location'] }}</dd>
                                        </div>
                                        @if ($job['salary'])
                                            <div>
                                                <dt>Gaji</dt>
                                                <dd>{{ $job['salary'] }}</dd>
                                            </div>
                                        @endif
                                    </dl>

                                    <div class="bkk-job-bottom">
                                        <p>Tutup {{ $job['deadline_label'] }}</p>
                                        <span class="bkk-detail-button">
                                            <span>Lihat Detail</span>
                                            <span class="bkk-arrow" aria-hidden="true">→</span>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </article>
                    </li>
                @endforeach
            </ol>

            <p class="bkk-filter-empty" data-bkk-empty-filter hidden>Tidak ada lowongan pada kategori ini.</p>

            <div class="bkk-footer">
                <p data-bkk-count aria-live="polite">Menampilkan {{ $jobs->count() }} lowongan terbaru.</p>
                <a href="{{ route('bkk') }}" class="bkk-all-link">
                    <span>{{ $copy['all_link'] }}</span>
                    <span class="bkk-arrow" aria-hidden="true">→</span>
                </a>
            </div>
        @else
            <div class="bkk-empty" data-bkk-reveal>
                <h3>Belum ada lowongan yang dibuka.</h3>
                <p>Informasi lowongan dan peluang karier terbaru akan muncul di sini.</p>
                <a href="{{ route('home') }}" class="bkk-all-link">
                    <span>Kembali ke beranda</span>
                    <span class="bkk-arrow" aria-hidden="true">→</span>
                </a>
            </div>
        @endif
    </div>
</section>
