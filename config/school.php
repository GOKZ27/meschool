<?php

return [

    /*
    |--------------------------------------------------------------------------
    | School identity
    |--------------------------------------------------------------------------
    |
    | Single source of truth for the brand shown across every Blade
    | component. Replace the values here rather than hard-coding them
    | inside the components.
    |
    */

    'name' => 'SMK ISLAMIYAH CIPUTAT',

    'short_name' => 'SMK ISLAMIYAH',

    'tagline' => 'Berbasis Islam, Berprestasi, dan Berjaya',

    'description' => 'Sekolah Menengah Kejuruan Islamiyah Ciputat menyiapkan generasi muda yang '
        .'berakhlak mulia, berpotensi, dan siap bekerja di dunia industri serta dunia usaha.',

    'domain' => 'www.smkislamiyahciputat.com',

    /*
    |--------------------------------------------------------------------------
    | Image folders
    |--------------------------------------------------------------------------
    |
    | Photos live in public/images/<subfolder>. See public/images/README.md
    | for the available subfolders and naming tips.
    |
    */

    'images' => [
        'base' => 'images',
        'folders' => [
            'hero' => 'hero',
            'principal' => 'principal',
            'guru' => 'guru',
            'program' => 'program',
            'berita' => 'berita',
            'galeri' => 'galeri',
            'kontak' => 'kontak',
            'fasilitas' => 'fasilitas',
            'logo' => 'logo',
            'bkk' => 'bkk',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Video folders
    |--------------------------------------------------------------------------
    |
    | Mirrors "images" for background clips, which are kept apart because they
    | are far larger than photos. See public/videos/README.md.
    |
    */

    'videos' => [
        'base' => 'videos',
        'folders' => [
            'hero' => 'hero',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | Point "path" at a real file in public/, e.g.
    | "images/logo/logo2.png". The artwork is dark navy on a transparent
    | background, so on the dark navbar it needs surface="light" to stay
    | legible (see <x-logo>). While "path" is null the component falls back to
    | a typographic placeholder instead.
    |
    */

    'logo' => [
        'path' => 'images/logo/logo2.png',
        'alt' => 'Logo SMK Islamiyah Ciputat',
    ],

    /*
    |--------------------------------------------------------------------------
    | Principal's greeting
    |--------------------------------------------------------------------------
    |
    | Copy and portrait for the greeting block under the hero banner.
    |
    |   'name'        leave null until the real name is known; the signature
    |                  then shows the role alone rather than a made-up name
    |   'role'        shown under the name, and as the fallback signature
    |   'photo'       portrait in public/images/principal/; while it is missing
    |                  the block falls back to a plain panel, never a broken img
    |   'photo_side'  'left' or 'right' — which side of the block the portrait
    |                  sits on. The text reveals from the opposite side, so
    |                  flipping this also flips the entrance direction
    |   'message'     one paragraph per array entry, written as plain prose
    |
    */

    'principal' => [
        'name' => null,
        'role' => 'Kepala Sekolah',
        'photo' => 'images/principal/kepala-sekolah.jpeg',
        'photo_side' => 'right',
        'message' => [
            'Assalamu\'alaikum warahmatullahi wabarakatuh, dan selamat datang di '.
                'SMK Islamiyah Ciputat.',

            'Anak-anak datang ke sini dengan bawaan yang berbeda-beda. Sebagian sudah tekun, '.
                'sebagian masih mencari arah. Tugas kami bukan menyamakan mereka, melainkan menolong '.
                'setiap anak menemukan versi dirinya yang layak: keterampilan yang benar-benar bisa '.
                'dipakai, akhlak yang bisa dipercaya, dan keberanian untuk terus mencoba.',

            'Karena itu proses belajar di sini tidak berhenti di buku. Program keahlian diarahkan '.
                'agar siswa terbiasa bekerja dengan alat, bahan, dan aturan yang nyata, sehingga '.
                'kepercayaan diri mereka tumbuh dari pekerjaan yang benar-benar pernah mereka '.
                'lakukan, bukan dari hafalan.',

            'Kami sadar mendidik bukan perjalanan singkat. Ia butuh kesabaran, komunikasi yang '.
                'terbuka, dan kemauan terus memperbaiki diri. Pintu kami terbuka untuk setiap orang '.
                'tua dan siswa, karena sekolah yang baik dibangun bersama.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Programs of study — home section
    |--------------------------------------------------------------------------
    |
    | The eyebrow, heading and standfirst of the "Program Keahlian" section on
    | the home page. The accords panel list itself is NOT duplicated here — the
    | section renders every entry of 'program_catalog.items' below, the single
    | catalogue of the six programs the school offers. Adding or renaming a
    | program means editing that catalogue (plus its route in routes/web.php).
    |
    */

    'programs' => [
        'eyebrow' => 'Program Keahlian',

        'title' => 'Belajar Terarah dengan Pilihan Jurusan Terbaik',

        'intro' => 'Ada 6 pilihan jurusan yang bisa calon siswa dan siswi pilih untuk belajar di '.
            'SMK Islamiyah Ciputat. Pilih yang paling sesuai dengan minat, lalu tumbuhkan jadi keahlian.',

        'slugs' => ['kuliner', 'dkv', 'tkj', 'ak', 'mp', 'bd'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Program landing pages
    |--------------------------------------------------------------------------
    |
    | The catalogue behind /program-keahlian/{slug} and the "Program Keahlian"
    | accordion on the home page. Every program that has a route gets one entry
    | here, keyed by the slug used in the URL. <x-programs-section> and the
    | navbar send visitors here, so the title must match the label they already
    | click on (TKJ stays TKJ, MP and BD stay deliberately generic — do not
    | guess their real names).
    |
    |   'name'          heading shown on the page, matching the menu/card label
    |   'icon'          a key of components/icon.blade.php
    |   'tagline'       one line under the title, in the hero band
    |   'description'   one short paragraph about the program
    |   'competencies'  "Kompetensi yang dipelajari" checklist
    |   'careers'       "Peluang karier" list
    |
    | 'eyebrow' is the hero band's kicker. The route order stays explicit: one
    | literal route per slug, so /program-keahlian/mp never misreads as a slug
    | route that happened to match.
    |
    */

    'program_catalog' => [
        'eyebrow' => 'Program Keahlian',

        'items' => [

            'kuliner' => [
                'name' => 'Kuliner',
                'icon' => 'utensils',
                'tagline' => 'Belajar mengolah, menyajikan, dan mengelola hidangan sesuai standar industri.',
                'description' => 'Program keahlian Kuliner membekali siswa dengan keterampilan memasak dan '.
                    'menyajikan hidangan Nusantara maupun internasional — dari dasar-dasar pengolahan '.
                    'bahan sampai manajemen dapur yang siap terjun ke dunia kerja.',
                'competencies' => [
                    'Pengolahan makanan Nusantara dan internasional',
                    'Teknik memilih, menyimpan, dan menangani bahan',
                    'Penyajian dan plating hidangan',
                    'Manajemen dapur dan pelayanan makanan',
                    'Kewirausahaan kuliner',
                ],
                'careers' => [
                    'Koki dan juru masak profesional',
                    'Baker dan pastry chef',
                    'Pengusaha kuliner atau food truck',
                    'Food styling dan content creator',
                    'Operator restoran, hotel, atau katering',
                ],
            ],

            'dkv' => [
                'name' => 'DKV',
                'icon' => 'pencil-square',
                'tagline' => 'Merancang komunikasi visual yang jelas, indah, dan berkesan.',
                'description' => 'DKV adalah cabang ilmu desain. Siswa belajar konsep komunikasi dan '.
                    'ungkapan kreatif dengan memakai berbagai elemen visual untuk menyampaikan pesan.',
                'competencies' => [
                    'Desain grafis dan tata letak (layout)',
                    'Ilustrasi manual dan digital',
                    'Identitas visual dan desain brand',
                    'Desain media digital dan cetak',
                    'Fotografi dasar untuk desain',
                ],
                'careers' => [
                    'Desainer grafis',
                    'Ilustrator',
                    'Desainer brand dan logo',
                    'Desainer media sosial',
                    'Operator pre-press dan percetakan',
                ],
            ],

            'tkj' => [
                'name' => 'TKJ',
                'icon' => 'server',
                'tagline' => 'Menguasai perangkat keras, sistem, dan jaringan komputer dari nol sampai siap kerja.',
                'description' => 'Program keahlian Teknik Komputer dan Jaringan membekali siswa dengan '.
                    'keterampilan merakit, merawat, dan mengelola komputer serta jaringan, termasuk '.
                    'administrasi perangkat dan sistem di lingkungan kerja nyata.',
                'competencies' => [
                    'Perakitan dan perawatan komputer',
                    'Instalasi dan konfigurasi jaringan LAN',
                    'Administrasi sistem dan server',
                    'Keamanan jaringan dasar',
                    'Sistem operasi dan perangkat lunak perkantoran',
                ],
                'careers' => [
                    'Teknisi komputer',
                    'Teknisi jaringan (network support)',
                    'Administrator sistem dan server',
                    'Tenaga instalasi jaringan',
                    'Helpdesk dan pendukung IT',
                ],
            ],

            'ak' => [
                'name' => 'AK',
                'icon' => 'calculator',
                'tagline' => 'Mengelola angka dan pembukuan keuangan yang rapi dan dapat dipertanggungjawabkan.',
                'description' => 'Program keahlian Akuntansi membekali siswa dengan keterampilan mencatat, '.
                    'mengelompokkan, dan menyajikan transaksi keuangan sesuai standar — dari pembukuan '.
                    'manual sampai aplikasi akuntansi digital.',
                'competencies' => [
                    'Akuntansi dasar dan siklus akuntansi',
                    'Pengelolaan kas dan pembukuan',
                    'Penggunaan aplikasi akuntansi',
                    'Perpajakan dasar',
                    'Administrasi perkantoran dan kearsipan',
                ],
                'careers' => [
                    'Staf akuntansi dan keuangan',
                    'Staf administrasi dan pembukuan',
                    'Juru buku (bookkeeper)',
                    'Operator kas dan keuangan',
                    'Pengelola keuangan UMKM',
                ],
            ],

            'mp' => [
                'name' => 'MP',
                'icon' => 'briefcase',
                'tagline' => 'Menjadi tenaga administrasi perkantoran yang rapi, cepat, dan komunikatif.',
                'description' => 'Program keahlian MP mempersiapkan siswa menjadi tenaga administrasi dan '.
                    'pengelola perkantoran yang terampil memakai aplikasi perkantoran dan menghadapi '.
                    'tuntutan dunia kerja yang semakin digital.',
                'competencies' => [
                    'Administrasi perkantoran dan kearsipan',
                    'Manajemen dan tata kelola',
                    'Komunikasi bisnis — surat dan laporan',
                    'Aplikasi pengolah dokumen, angka, dan presentasi',
                    'Pelayanan pelanggan',
                ],
                'careers' => [
                    'Staf administrasi',
                    'Sekretaris dan asisten perkantoran',
                    'Operator data dan arsip',
                    'Staf keuangan dan pembukuan',
                    'Resepsionis dan penerima tamu',
                ],
            ],

            'bd' => [
                'name' => 'BD',
                'icon' => 'chart-bar',
                'tagline' => 'Mengembangkan jiwa usaha dan kemampuan mengelola bisnis dengan teknologi.',
                'description' => 'Program keahlian BD membekali siswa dengan keterampilan merencanakan, '.
                    'menjalankan, dan mengembangkan usaha — dipadukan dengan pemanfaatan teknologi untuk '.
                    'pemasaran dan transaksi di era digital.',
                'competencies' => [
                    'Kewirausahaan dan perencanaan bisnis',
                    'Pemasaran produk dan jasa',
                    'Komunikasi bisnis dan pelayanan pelanggan',
                    'Administrasi transaksi dan keuangan usaha',
                    'Pemanfaatan teknologi untuk bisnis',
                ],
                'careers' => [
                    'Wirausahawan',
                    'Staf pemasaran dan penjualan',
                    'Pengelola marketplace dan toko daring',
                    'Staf administrasi bisnis',
                    'Staf logistik dan distribusi',
                ],
            ],

            'animasi' => [
                'name' => 'Animasi',
                'icon' => 'film',
                'tagline' => 'Menghidupkan ide lewat gerak: gambar, karakter, dan cerita.',
                'description' => 'Jurusan Animasi fokus dalam mempelajari teori dan teknik dalam membuat '.
                    'animasi 2D dan 3D, animasi film, animasi game, dan bidang lainnya yang menggunakan '.
                    'konten animasi.',
                'competencies' => [
                    'Animasi 2D dan 3D',
                    'Perancangan karakter dan gerak',
                    'Storyboard dan narasi visual',
                    'Animasi untuk film dan game',
                    'Rendering dan koreksi warna',
                ],
                'careers' => [
                    'Animator',
                    'Desainer karakter',
                    'Storyboard artist',
                    'Motion graphic designer',
                    'Seniman efek visual (VFX)',
                ],
            ],

            'tkjt' => [
                'name' => 'TKJT',
                'icon' => 'signal',
                'tagline' => 'Menghubungkan dunia lewat jaringan, telekomunikasi, dan transmisi data.',
                'description' => 'Teknik Jaringan Komputer dan Telekomunikasi adalah jurusan yang berisi '.
                    'kompetensi yang mendasari penguasaan keahlian jaringan komputer dan telekomunikasi.',
                'competencies' => [
                    'Jaringan komputer dasar sampai lanjut',
                    'Instalasi dan pemeliharaan fiber optik',
                    'Telekomunikasi dan transmisi data',
                    'Administrasi sistem dan server',
                    'Keamanan jaringan',
                ],
                'careers' => [
                    'Teknisi jaringan dan fiber optik',
                    'Teknisi telekomunikasi',
                    'Network administrator',
                    'Staf ISP atau operator telekomunikasi',
                    'Tenaga instalasi dan pemeliharaan',
                ],
            ],

            'pplg' => [
                'name' => 'PPLG',
                'icon' => 'code-bracket',
                'tagline' => 'Membangun aplikasi, situs, dan gim yang benar-benar dipakai orang.',
                'description' => 'Jurusan PPLG berhubungan dengan software komputer seperti pembuatan '.
                    'aplikasi, website, game dan semua yang berhubungan dengan pemrograman.',
                'competencies' => [
                    'Logika dan dasar pemrograman',
                    'Pembuatan aplikasi web dan mobile',
                    'Basis data dan API',
                    'Pembuatan game',
                    'Pengujian dan pemeliharaan aplikasi',
                ],
                'careers' => [
                    'Programmer dan developer aplikasi',
                    'Pengembang web',
                    'Pengembang game',
                    'Frontend dan UI/UX',
                    'Testing dan pendukung aplikasi',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | News
    |--------------------------------------------------------------------------
    |
    | The archive behind /berita and the preview blocks on the home page.
    |
    | Categories are a catalogue here rather than a table: the label, the icon
    | and the tone every chip, filter and card uses resolve from this one list,
    | so renaming a category is a config edit and never a database write.
    |
    | 'icon' must be a key of components/icon.blade.php — there is no icon
    | library, so an unknown name renders an empty box. 'tone' picks the
    | palette the chip is drawn in: "school" for green, "gold" for gold.
    |
    | 'home' and 'archive' are the section headings, matching the shape the
    | programs section uses (eyebrow / title / intro).
    |
    */

    'news' => [
        'per_page' => 12,

        // Indonesian month names for the date lines on cards and articles —
        // the framework ships English only, and the UI copy is Indonesian.
        'months' => [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ],

        'categories' => [
            'akademik' => ['label' => 'Akademik', 'icon' => 'graduation-cap', 'tone' => 'school'],
            'prestasi' => ['label' => 'Prestasi', 'icon' => 'award', 'tone' => 'gold'],
            'kegiatan' => ['label' => 'Kegiatan', 'icon' => 'user-group', 'tone' => 'school'],
            'pengumuman' => ['label' => 'Pengumuman', 'icon' => 'document', 'tone' => 'gold'],
            'bkk' => ['label' => 'BKK', 'icon' => 'briefcase', 'tone' => 'school'],
            'spmb' => ['label' => 'SPMB', 'icon' => 'sparkles', 'tone' => 'gold'],
        ],

        'home' => [
            'eyebrow' => 'Kabar Sekolah',
            'title' => 'Berita Terkini SMK Islamiyah Ciputat',
            'intro' => 'Kegiatan, pengumuman, dan prestasi warga sekolah — dirangkum untuk Anda, '
                .'lengkap dengan video dan galeri foto.',

            'featured_label' => 'Berita Utama',
            'latest_title' => 'Berita Terbaru',
            'latest_archive' => 'Semua',
            'latest_tabs' => ['prestasi', 'kegiatan', 'akademik'],
            'past_title' => 'Masa Lampau',
            'trending_title' => 'Trending',
            'trending_note' => 'Paling banyak dibaca',
            'browse_title' => 'Jelajahi per kategori',
            'read_more' => 'Baca',
            'gallery_title' => 'Kegiatan & Karya Peserta Didik',
            'gallery_panels' => [
                ['title' => 'Proyek Coding Siswa', 'desc' => 'Aplikasi dan website karya siswa dari kelas PPLG.', 'photo' => 'gallery/proyek-coding-siswa.jpg'],
                ['title' => 'Karya Seni Digital', 'desc' => 'Desain grafis dan ilustrasi digital siswa DKV.', 'photo' => 'gallery/karya-seni-digital.jpg'],
                ['title' => 'Kompetisi Robotik', 'desc' => 'Karya dan robotika siswa kelas TKJ & TKJT.', 'photo' => 'gallery/kompetisi-robotik.jpg'],
                ['title' => 'Pameran Karya Desain', 'desc' => 'Poster, kemasan, dan prototipe dari kelas DKV.', 'photo' => 'gallery/pameran-karya-desain.jpg'],
                ['title' => 'Kegiatan Pramuka & Olahraga', 'desc' => 'Dinamika tim di lapangan dan ekskul pramuka.', 'photo' => 'gallery/kegiatan-pramuka-olahraga.jpg'],
                ['title' => 'Lomba Inovasi Teknologi', 'desc' => 'Proyek inovasi yang dibawa siswa ke ajang lomba.', 'photo' => 'gallery/lomba-inovasi-teknologi.jpg'],
            ],
            'all_link' => 'Lihat Semua Karya',
            'latest_per_page' => 5,
        ],

        'archive' => [
            'eyebrow' => 'Arsip Berita',
            'title' => 'Semua Berita',
            'intro' => 'Telusuri kabar sekolah dari yang terbaru sampai arsip lama — '
                .'saring berdasarkan kata kunci, kategori, atau tahun.',

            'search_placeholder' => 'Cari berita...',
            'search_label' => 'Cari berita',
            'category_label' => 'Kategori',
            'year_label' => 'Tahun',
            'all_years' => 'Semua tahun',
            'reset' => 'Tampilkan semua',
            'empty' => 'Belum ada berita yang cocok dengan pencarian Anda.',
            'related_title' => 'Berita Terkait',

            // The phone-only feed that replaces the two-column archive below
            // the lg breakpoint: two medium stories, a tight row of four
            // thumbnails, the popular category tags and a "next page" button.
            'tags_title' => 'Tag Terpopuler',
            'more' => 'Selengkapnya',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Bursa Kerja Khusus (BKK)
    |--------------------------------------------------------------------------
    |
    | Job listings for students and alumni. There is no jobs table yet, so the
    | entries below are the working copy — the same approach as "staff". The
    | BKK section on the home page, the /bkk archive and every /bkk/{slug}
    | detail page all read from here.
    |
    | A status is derived from 'deadline' at render time: 'open' while the
    | closing date is ahead, 'closing' once it sits inside "closing_days",
    | and 'closed' the day after it passes. 'logo' points at a file in
    | public/images/bkk/ (or a full URL); when the file is missing the card
    | falls back to a monogram from the company name.
    |
    */

    'bkk' => [
        'per_page' => 6,
        'home_count' => 4,
        'closing_days' => 7,

        // Categories are a catalogue here rather than a table, so renaming one
        // is a config edit and never a database write.
        'categories' => [
            'perhotelan' => ['label' => 'Kuliner & Perhotelan'],
            'retail' => ['label' => 'Retail & Pemasaran'],
            'jaringan' => ['label' => 'Jaringan & Teknisi'],
            'desain' => ['label' => 'Desain & Kreatif'],
            'administrasi' => ['label' => 'Administrasi & Keuangan'],
        ],

        'status' => [
            'open' => 'Open',
            'closing' => 'Segera Berakhir',
            'closed' => 'Ditutup',
        ],

        'home' => [
            'eyebrow' => 'Peluang Karier',
            'title' => 'Bursa Kerja Khusus (BKK)',
            'intro' => 'Temukan informasi lowongan kerja dan peluang karier bagi siswa dan alumni.',
            'all_link' => 'Lihat Semua Lowongan',
        ],

        'archive' => [
            'eyebrow' => 'Bursa Kerja Khusus',
            'title' => 'Lowongan Kerja',
            'intro' => 'Lowongan dari mitra BKK SMK Islamiyah Ciputat untuk siswa dan alumni — '
                .'saring berdasarkan kata kunci, bidang, lokasi, atau status pendaftaran.',

            'search_placeholder' => 'Cari lowongan atau perusahaan...',
            'search_label' => 'Cari',
            'all_categories' => 'Semua bidang',
            'all_locations' => 'Semua lokasi',
            'all_statuses' => 'Semua status',
            'reset' => 'Tampilkan semua',
            'empty' => 'Belum ada lowongan yang cocok dengan pencarian Anda.',
        ],

        'detail' => [
            'overview' => 'Informasi Lowongan',
            'description' => 'Deskripsi Pekerjaan',
            'requirements' => 'Persyaratan',
            'qualifications' => 'Kualifikasi',
            'benefits' => 'Benefit',
            'how_to_apply' => 'Cara Melamar',
            'apply_label' => 'Lamar Sekarang',
            'deadline_label' => 'Batas Pendaftaran',
        ],

        'jobs' => [
            [
                'slug' => 'teknisi-jaringan-fiber-optik',
                'position' => 'Teknisi Jaringan & Fiber Optik',
                'company' => 'PT Nusantara Fiber',
                'logo' => 'images/bkk/nusantara-fiber.png',
                'category' => 'jaringan',
                'location' => 'Ciputat, Tangerang Selatan',
                'type' => 'Full-time',
                'salary' => 'Rp 4.500.000 – 5.200.000',
                'deadline' => '2026-11-05',
                'published_at' => '2026-10-02',
                'description' => [
                    'PT Nusantara Fiber membuka lowongan teknisi jaringan untuk mendukung pemasangan '
                        .'dan perawatan jaringan fiber optik di wilayah Tangerang Selatan dan sekitarnya.',
                    'Kandidat yang terpilih akan bertugas di lapangan bersama tim teknis, menangani '
                        .'instalasi kabel fiber, perangkat OLT/ONU, serta menjaga kualitas layanan '
                        .'internet pelanggan.',
                ],
                'requirements' => [
                    'Lulusan SMK jurusan TKJ atau TKJT — fresh graduate dipersilakan',
                    'Menguasai crimping kabel UTP dan splicing fiber optik',
                    'Siap bekerja di lapangan dan berpindah lokasi',
                ],
                'qualifications' => [
                    'Memiliki sertifikat keahlian jaringan (BNSP) menjadi nilai tambah',
                    'Terbiasa dengan perangkat Mikrotik dan konfigurasi jaringan dasar',
                    'Bersedia bekerja dalam shift dan menjaga keselamatan kerja',
                ],
                'benefits' => [
                    'Gaji pokok plus tunjangan lapangan',
                    'BPJS Ketenagakerjaan dan Kesehatan',
                    'Pelatihan teknik fiber optik lanjutan',
                    'Kesempatan naik menjadi teknisi senior',
                ],
                'how_to_apply' => [
                    'Kirim lamaran dan CV melalui tombol lamaran di bawah.',
                    'Kandidat yang memenuhi syarat akan dihubungi untuk tes praktik splicing dan wawancara.',
                ],
                'apply' => 'mailto:info@smkislamiyahciputat.com',
            ],
            [
                'slug' => 'desainer-grafis-content-creator',
                'position' => 'Desainer Grafis & Content Creator',
                'company' => 'CV Karya Visual Studio',
                'logo' => 'images/bkk/karya-visual.png',
                'category' => 'desain',
                'location' => 'Ciputat, Tangerang Selatan',
                'type' => 'Full-time',
                'salary' => 'Rp 4.000.000 – 4.800.000',
                'deadline' => '2026-10-30',
                'published_at' => '2026-10-01',
                'description' => [
                    'CV Karya Visual Studio — studio desain dan produksi konten — mencari desainer '
                        .'grafis untuk proyek branding, media sosial, dan cetak bagi klien UMKM di '
                        .'wilayah Tangerang Selatan.',
                    'Posisi ini cocok bagi lulusan DKV yang ingin menumbuhkan portofolio di lingkungan '
                        .'studio yang kecil dan cepat.',
                ],
                'requirements' => [
                    'Lulusan SMK jurusan DKV atau Animasi',
                    'Mahir Adobe Illustrator, Photoshop, dan Figma',
                    'Memiliki portofolio desain sederhana (tugas sekolah boleh dilampirkan)',
                ],
                'qualifications' => [
                    'Terbiasa membuat konten untuk Instagram dan TikTok',
                    'Mengerti dasar-dasar tipografi dan tata letak cetak',
                    'Bisa mengambil foto produk sederhana dengan kamera HP',
                ],
                'benefits' => [
                    'Gaji pokok plus bonus proyek',
                    'Perangkat kerja (PC dan tablet grafis) disediakan',
                    'Jadwal kerja fleksibel (hybrid)',
                    'Cuti bersama di hari raya',
                ],
                'how_to_apply' => [
                    'Kirim CV dan tautan portofolio melalui tombol lamaran di bawah.',
                    'Tahap seleksi: portofolio → tes desain → wawancara.',
                ],
                'apply' => 'mailto:info@smkislamiyahciputat.com',
            ],
            [
                'slug' => 'pramusaji-restoran-bintaro',
                'position' => 'Pramusaji (Waiter/Waitress)',
                'company' => 'Restoran Soerati Bintaro',
                'logo' => null,
                'category' => 'perhotelan',
                'location' => 'Bintaro, Tangerang Selatan',
                'type' => 'Full-time',
                'salary' => 'Rp 3.800.000 – 4.200.000',
                'deadline' => '2026-11-15',
                'published_at' => '2026-09-28',
                'description' => [
                    'Restoran Soerati Bintaro membuka lowongan pramusaji untuk melayani tamu dengan '
                        .'standar pelayanan yang ramah dan rapi.',
                    'Kandidat akan mendapat pelatihan service excellence sebelum diterjunkan ke area '
                        .'restoran.',
                ],
                'requirements' => [
                    'Lulusan SMK jurusan Kuliner atau Perhotelan — fresh graduate dipersilakan',
                    'Berpenampilan rapi, komunikatif, dan menyukai pelayanan',
                    'Siap bekerja dalam shift, termasuk akhir pekan',
                ],
                'qualifications' => [
                    'Memahami tata letak meja (table setting) dasar',
                    'Bersedia belajar menu dan standar penyajian',
                    'Tinggi dan berat badan proporsional',
                ],
                'benefits' => [
                    'Gaji pokok plus uang makan dan tip',
                    'Seragam kerja disediakan',
                    'BPJS Ketenagakerjaan',
                    'Jenjang promosi ke posisi supervisor',
                ],
                'how_to_apply' => [
                    'Serahkan lamaran langsung ke resepsionis Restoran Soerati Bintaro sambil '
                        .'menyebutkan kode lowongan BKK-SMKIC.',
                    'Atau kirim lamaran melalui tombol lamaran di bawah.',
                ],
                'apply' => 'mailto:info@smkislamiyahciputat.com',
            ],
            [
                'slug' => 'staf-kasir-administrasi',
                'position' => 'Staf Kasir & Administrasi',
                'company' => 'Retail Purnama',
                'logo' => null,
                'category' => 'administrasi',
                'location' => 'Pondok Ranji, Ciputat',
                'type' => 'Full-time',
                'salary' => 'Rp 3.500.000 – 4.000.000',
                'deadline' => '2026-10-09',
                'published_at' => '2026-09-25',
                'description' => [
                    'Retail Purnama mencari staf kasir dan administrasi untuk mengelola transaksi '
                        .'harian, stok, dan laporan penjualan toko.',
                    'Posisi ini melatih ketelitian, kejujuran, dan keterampilan menangani pelanggan '
                        .'di depan kasir.',
                ],
                'requirements' => [
                    'Lulusan SMK jurusan Akuntansi, MP, atau administrasi',
                    'Jujur, teliti, dan terbiasa bekerja dengan angka',
                    'Bisa mengoperasikan komputer dan mesin kasir',
                ],
                'qualifications' => [
                    'Memahami dasar pembukuan dan laporan stok',
                    'Cekatan dan ramah saat melayani pelanggan',
                    'Bersedia bekerja dengan sistem shift',
                ],
                'benefits' => [
                    'Gaji pokok plus insentif penjualan',
                    'Pelatihan perhitungan stok dan belanja barang',
                    'BPJS Ketenagakerjaan',
                ],
                'how_to_apply' => [
                    'Daftar melalui tombol lamaran di bawah dengan menyertakan CV dan fotokopi ijazah.',
                    'Proses seleksi: administrasi → tes hitung → wawancara singkat.',
                ],
                'apply' => 'mailto:info@smkislamiyahciputat.com',
            ],
            [
                'slug' => 'teknisi-komputer-printer',
                'position' => 'Teknisi Komputer & Printer',
                'company' => 'CV Klik Komputer',
                'logo' => 'images/bkk/klik-komputer.png',
                'category' => 'jaringan',
                'location' => 'Ciputat Timur, Tangerang Selatan',
                'type' => 'Full-time',
                'salary' => 'Rp 4.200.000 – 4.800.000',
                'deadline' => '2026-10-09',
                'published_at' => '2026-09-20',
                'description' => [
                    'CV Klik Komputer — toko dan jasa servis komputer — membutuhkan teknisi untuk '
                        .'menangani perawatan dan perbaikan PC, laptop, dan printer pelanggan.',
                    'Pekerjaan mencakup instalasi sistem operasi, perakitan perangkat, dan service '
                        .'printer inkjet maupun laser.',
                ],
                'requirements' => [
                    'Lulusan SMK jurusan TKJ atau TKJT',
                    'Menguasai instalasi Windows, driver, dan perakitan PC',
                    'Terampil membongkar-pasang laptop dan printer',
                ],
                'qualifications' => [
                    'Mengetahui perbaikan printer Epson dan Canon',
                    'Jujur dan bisa menjelaskan kerusakan kepada pelanggan',
                    'Siap menerima panggilan servis ke lokasi pelanggan',
                ],
                'benefits' => [
                    'Gaji pokok plus komisi servis',
                    'Tools kit disediakan',
                    'BPJS Ketenagakerjaan',
                    'Pelatihan servis printer dan jaringan setiap tahun',
                ],
                'how_to_apply' => [
                    'Kirim lamaran beserta CV melalui tombol lamaran di bawah.',
                    'Seleksi meliputi tes instalasi perangkat dan wawancara langsung di toko.',
                ],
                'apply' => 'mailto:info@smkislamiyahciputat.com',
            ],
            [
                'slug' => 'barista-kedai-kopi-senja',
                'position' => 'Barista & Kasir',
                'company' => 'Kedai Kopi Senja',
                'logo' => null,
                'category' => 'perhotelan',
                'location' => 'Ciputat, Tangerang Selatan',
                'type' => 'Part-time',
                'salary' => 'Rp 2.800.000 – 3.200.000',
                'deadline' => '2026-10-25',
                'published_at' => '2026-09-15',
                'description' => [
                    'Kedai Kopi Senja membuka posisi barista untuk menjaga bar, meracik kopi, dan '
                        .'membantu operasional dapur.',
                    'Cocok untuk lulusan Kuliner yang ingin belajar dunia specialty coffee dari nol — '
                        .'kedai menyediakan pelatihan barista lengkap dengan sertifikat.',
                ],
                'requirements' => [
                    'Lulusan SMK jurusan Kuliner atau Perhotelan',
                    'Menjaga kebersihan area kerja dan menerapkan higiene',
                    'Siap bekerja shift sore hingga malam',
                ],
                'qualifications' => [
                    'Mengetahui dasar meracik kopi menjadi nilai tambah',
                    'Komunikatif dan ramah terhadap pelanggan',
                    'Bisa cepat menghafal menu',
                ],
                'benefits' => [
                    'Upah per bulan plus uang transportasi',
                    'Pelatihan barista bersertifikat gratis',
                    'Gratis minuman saat jam kerja',
                    'Jadwal part-time fleksibel bagi yang masih kuliah',
                ],
                'how_to_apply' => [
                    'Kirim lamaran dan CV singkat melalui tombol lamaran di bawah.',
                    'Posisi dibuka hingga kuota terpenuhi — lamaran awal diprioritaskan.',
                ],
                'apply' => null,
            ],
            [
                'slug' => 'pramuniaga-gerai-smartphone',
                'position' => 'Pramuniaga Gerai Smartphone',
                'company' => 'Era Ponsel',
                'logo' => 'images/bkk/era-ponsel.png',
                'category' => 'retail',
                'location' => 'BSD, Tangerang Selatan',
                'type' => 'Full-time',
                'salary' => 'Rp 3.800.000 – 4.500.000',
                'deadline' => '2026-10-29',
                'published_at' => '2026-09-10',
                'description' => [
                    'Era Ponsel membuka lowongan pramuniaga untuk gerai BSD, melayani konsultasi '
                        .'produk, transaksi, dan pelayanan purnajual.',
                    'Lulusan SMK dengan kemampuan komunikasi yang baik akan dibina menjadi tim '
                        .'penjualan yang dapat dipercaya.',
                ],
                'requirements' => [
                    'Lulusan SMK semua jurusan (nilai tambah: MP, DKV, TKJ)',
                    'Ramah, jujur, dan menyukai tantangan penjualan',
                    'Bersedia bekerja dengan sistem shift di gerai',
                ],
                'qualifications' => [
                    'Mengenal spesifikasi dasar smartphone dan aksesori',
                    'Terbiasa menggunakan media sosial',
                    'Target-oriented dan menjaga kerapian display',
                ],
                'benefits' => [
                    'Gaji pokok plus komisi penjualan',
                    'BPJS Ketenagakerjaan dan Kesehatan',
                    'Seragam gerai disediakan',
                ],
                'how_to_apply' => [
                    'Serahkan lamaran ke HRD Era Ponsel Gerai BSD setiap hari kerja pukul 10.00–16.00.',
                    'Atau daftar online melalui tombol lamaran di bawah.',
                ],
                'apply' => 'mailto:info@smkislamiyahciputat.com',
            ],
            [
                'slug' => 'staf-produksi-roti-pastry',
                'position' => 'Staf Produksi Roti & Pastry',
                'company' => 'Bakery Sahabat',
                'logo' => null,
                'category' => 'perhotelan',
                'location' => 'Serpong, Tangerang Selatan',
                'type' => 'Full-time',
                'salary' => 'Rp 3.600.000 – 4.100.000',
                'deadline' => '2026-09-28',
                'published_at' => '2026-09-05',
                'description' => [
                    'Bakery Sahabat Serpong membuka lowongan staf produksi untuk membantu pembuatan '
                        .'roti, kue, dan pastry di dapur produksi.',
                    'Posisi ini menuntut kebersihan, ketepatan resep, dan kesiapan bekerja dalam '
                        .'suasana dapur yang hangat dan cepat.',
                ],
                'requirements' => [
                    'Lulusan SMK jurusan Kuliner atau Tata Boga',
                    'Terampil dasar mengadon, memanggang, dan menghias',
                    'Wajib sehat dan memegang standar higiene pangan',
                ],
                'qualifications' => [
                    'Menguasai teknik pastry dasar menjadi nilai tambah',
                    'Teliti dalam menimbang dan mencatat bahan',
                    'Siap bekerja shift pagi dan mengikuti SOP dapur',
                ],
                'benefits' => [
                    'Gaji pokok plus bonus produksi',
                    'Makan siang disediakan',
                    'Pelatihan sertifikasi keamanan pangan',
                ],
                'how_to_apply' => [
                    'Lamaran dikirim melalui tombol lamaran di bawah dengan melampirkan CV dan '
                        .'portofolio karya (jika ada).',
                ],
                'apply' => null,
            ],
            [
                'slug' => 'asisten-administrasi-kantor',
                'position' => 'Asisten Administrasi Kantor (Magang)',
                'company' => 'CV Berkah Prima',
                'logo' => null,
                'category' => 'administrasi',
                'location' => 'Ciputat, Tangerang Selatan',
                'type' => 'Magang',
                'salary' => null,
                'deadline' => '2026-10-12',
                'published_at' => '2026-09-01',
                'description' => [
                    'CV Berkah Prima menerima siswa dan alumni SMK untuk program magang sebagai '
                        .'asisten administrasi: mengelola arsip, surat-menyurat, dan data pemesanan.',
                    'Program ini menjadi jembatan dari Praktik Kerja Lapangan (PKL) menuju '
                        .'rekrutmen pegawai tetap.',
                ],
                'requirements' => [
                    'Siswa kelas 12 atau alumni SMK jurusan MP atau AK',
                    'Menguasai Microsoft Word dan Excel dasar',
                    'Rapi, jujur, dan bisa menjaga kerahasiaan data',
                ],
                'qualifications' => [
                    'Terbiasa mengelola surat dan arsip digital',
                    'Cepat mengetik minimal 25 kata per menit',
                    'Siap magang minimal 3 bulan',
                ],
                'benefits' => [
                    'Uang saku bulanan',
                    'Sertifikat magang dan surat pengalaman',
                    'Bimbingan langsung oleh supervisor kantor',
                    'Prioritas menjadi pegawai tetap',
                ],
                'how_to_apply' => [
                    'Daftar melalui BKK SMK Islamiyah Ciputat dengan menyertakan rapor dan surat '
                        .'pengantar sekolah.',
                    'Seleksi: tes ketik dan wawancara ringan.',
                ],
                'apply' => null,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SPMB — Seleksi Penerimaan Murid Baru
    |--------------------------------------------------------------------------
    |
    | The admission block that closes the home page: a deep-green hero with the
    | student photo on the right, followed by the "why this school" benefits
    | band. 'bg_photo' reuses the school photo the hero already serves (the only
    | building photo that ships with the repo); 'photo' is the student shot for
    | the right-hand panel. Both resolve through Images::existingUrl(), so a
    | missing file falls back to a gradient / monogram instead of a broken layer.
    |
    |   'title'         the short "Informasi SPMB" heading
    |   'school_name'   the school line under it, painted in mint
    |   'subtitle'      the academic year
    |   'register_url'  registration destination; null falls back to the
    |                   WhatsApp link in school.contact
    |   'benefits'      the five cards under the hero: label, title, intro and
    |                   items[] of icon / title / description
    |
    */

    'spmb' => [
        'bg_photo' => 'images/hero/hero1.jpeg',
        'photo' => 'images/berita/mos-2026-kelas-dasar.jpg',
        'photo_alt' => 'Siswa baru SMK Islamiyah Ciputat mengikuti pengenalan kelas saat masa orientasi sekolah',
        'overlay' => 'bg-gradient-to-b from-school-950/90 via-school-950/60 to-school-950/90',
        'eyebrow' => 'SPMB',
        'title' => 'Informasi SPMB',
        'school_name' => 'SMK Islamiyah Ciputat',
        'subtitle' => 'Tahun Ajaran 2027/2028',
        'description' => 'SMK Islamiyah Ciputat membuka kesempatan bagi calon peserta didik baru '
            .'untuk bergabung dan berkembang dalam lingkungan pendidikan yang aktif, modern, dan '
            .'berorientasi pada dunia kerja.',
        'button_label' => 'Daftar Sekarang',
        'register_url' => null,
        'link_label' => 'Lihat Informasi SPMB',

        'benefits' => [
            'label' => 'Kenapa memilih SMK Islamiyah Ciputat?',
            'title' => 'Tempat Terbaik untuk Masa Depanmu',
            'description' => 'Kami hadir dengan pendidikan berkualitas, fasilitas lengkap, dan program '
                .'keahlian yang sesuai dengan kebutuhan dunia kerja.',

            'items' => [
                [
                    'icon' => 'graduation-cap',
                    'title' => 'Program Keahlian Unggulan',
                    'description' => 'Pilihan jurusan sesuai minat dan bakat untuk masa depan kariermu.',
                ],
                [
                    'icon' => 'user-group',
                    'title' => 'Tenaga Pendidik Profesional',
                    'description' => 'Dibimbing oleh guru berpengalaman dan kompeten di bidangnya.',
                ],
                [
                    'icon' => 'desktop',
                    'title' => 'Fasilitas Lengkap',
                    'description' => 'Ruang kelas nyaman, laboratorium modern, dan akses internet.',
                ],
                [
                    'icon' => 'briefcase',
                    'title' => 'Kerja Sama Industri',
                    'description' => 'Peluang magang dan penyaluran kerja setelah lulus.',
                ],
                [
                    'icon' => 'chart-bar',
                    'title' => 'Lingkungan Islami dan Berkarakter',
                    'description' => 'Membentuk pribadi unggul, disiplin, dan berakhlak mulia.',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SPMB landing page
    |--------------------------------------------------------------------------
    |
    | The copy behind /spmb, the page the home banner sends its calls-to-action
    | to. 'hero' reuses the home banner's photo + overlay so both places read as
    | the same campaign; the rest is the registration flow.
    |
    */

    'spmb_page' => [
        'hero_eyebrow' => 'SPMB',
        'title' => 'Penerimaan Peserta Didik Baru',
        'subtitle' => 'Tahun Ajaran 2027/2028',
        'intro' => 'SMK Islamiyah Ciputat membuka kesempatan bagi calon peserta didik baru untuk '.
            'bergabung dan berkembang dalam lingkungan pendidikan yang aktif, modern, dan '.
            'berorientasi pada dunia kerja.',

        'steps_label' => 'Alur Pendaftaran',
        'steps' => [
            [
                'title' => 'Isi Formulir Pendaftaran',
                'description' => 'Daftarkan diri lewat panitia SPMB di sekolah atau hubungi nomor kontak '.
                    'resmi untuk mendapatkan formulir.',
            ],
            [
                'title' => 'Lengkapi Berkas',
                'description' => 'Siapkan dan serahkan seluruh berkas pendaftaran sesuai daftar '.
                    'persyaratan yang sudah diumumkan.',
            ],
            [
                'title' => 'Ikuti Seleksi',
                'description' => 'Calon peserta mengikuti tahapan seleksi sesuai jadwal yang '.
                    'disampaikan panitia.',
            ],
            [
                'title' => 'Pengumuman dan Daftar Ulang',
                'description' => 'Hasil seleksi diumumkan secara resmi, lalu peserta yang diterima '.
                    'melakukan daftar ulang untuk memastikan tempatnya.',
            ],
        ],

        'requirements_label' => 'Persyaratan Pendaftaran',
        'requirements' => [
            'Fotokopi kartu keluarga (KK)',
            'Fotokopi akta kelahiran',
            'Fotokopi ijazah atau surat keterangan lulus SMP/MTs',
            'Rapor semester 1 sampai 5',
            'Pas foto berwarna 3×4 sebanyak 2 lembar',
        ],

        'cta_title' => 'Siap bergabung bersama kami?',
        'cta_description' => 'Sampaikan minat Anda sekarang lalu lengkapi berkasnya — '.
            'bagi yang bingung memilih jurusan, tim kami siap memberi masukan.',
        'register_label' => 'Daftar Sekarang',
        'contact_label' => 'Hubungi Panitia',
    ],

    /*
    |--------------------------------------------------------------------------
    | Teachers and staff
    |--------------------------------------------------------------------------
    |
    | The people behind the teaching, shown as a horizontal card slider on the
    | home page. There is no staff table yet, so the entries below are the
    | working copy: change a name, a photo or a paragraph here and every card
    | and profile popup follows.
    |
    |   'name'         shown on the card and in the popup. A trailing academic
    |                  title (", S.Pd.") is kept, while the monogram placeholder
    |                  is built from the initials of the name in front of it
    |   'position'     job title, i.e. the subject or the role
    |   'photo'        portrait in public/images/guru/; while it is missing the
    |                  card falls back to a monogram, never a broken img
    |   'description'  one short paragraph, shown on the card and in the popup
    |   'expertise'    bullet list in the popup only; may be left out
    |
    | 'eyebrow', 'title' and 'intro' are the section heading, matching the
    | shape the other sections use.
    |
    */

    'staff' => [
        'eyebrow' => 'Tenaga Kependidikan',

        'title' => 'Kenali tenaga pendidik dan kependidikan yang menemanimu bertumbuh',

        'items' => [
            [
                'name' => 'Ahmad Fauzan, S.Pd.',
                'position' => 'Guru Produktif TKJ',
                'photo' => 'images/guru/ahmad-fauzan.jpg',
                'description' => 'Mengajar bidang jaringan komputer dan administrasi sistem, dari '.
                    'pemasangan perangkat sampai mengelola server kelas.',
                'expertise' => ['Jaringan komputer', 'Administrasi sistem', 'Linux'],
            ],
            [
                'name' => 'Siti Nurhaliza, S.Pd.',
                'position' => 'Guru Produktif DKV',
                'photo' => 'images/guru/siti-nurhaliza.jpg',
                'description' => 'Membimbing siswa dalam desain grafis, dari tata letak digital sampai '.
                    'desain brand yang siap dicetak.',
                'expertise' => ['Desain grafis', 'Tipografi', 'Desain digital'],
            ],
            [
                'name' => 'Rizky Pratama, S.Kom.',
                'position' => 'Guru Produktif PPLG',
                'photo' => 'images/guru/rizky-pratama.jpg',
                'description' => 'Mengajar pengembangan aplikasi dan gim, dari halaman web sederhana '.
                    'sampai basis data yang bisa dirawat.',
                'expertise' => ['Web development', 'Basis data', 'Version control'],
            ],
            [
                'name' => 'Dewi Lestari, S.Pd.',
                'position' => 'Guru Matematika',
                'photo' => 'images/guru/dewi-lestari.jpg',
                'description' => 'Menjelaskan matematika sebagai bahasa yang sederhana untuk berpikir '.
                    'logis dan menyelesaikan masalah sehari-hari.',
                'expertise' => ['Matematika terapan', 'Statistik dasar', 'Bimbingan belajar'],
            ],
            [
                'name' => 'Muhammad Arif, S.Pd.',
                'position' => 'Guru Produktif TKJT',
                'photo' => 'images/guru/muhammad-arif.jpg',
                'description' => 'Mengajar teknik jaringan dan telekomunikasi, jalur yang menghubungkan '.
                    'sekolah dengan bidang transmisi dan jaringan.',
                'expertise' => ['Telekomunikasi', 'Fiber optik', 'Jaringan fiber'],
            ],
            [
                'name' => 'Nur Aisyah, S.Pd.I.',
                'position' => 'Guru Pendidikan Agama Islam',
                'photo' => 'images/guru/nur-aisyah.jpg',
                'description' => 'Menjaga nilai dan adab yang menyertai keterampilan teknik, supaya siswa '.
                    'berkarya dengan cara yang bertanggung jawab.',
                'expertise' => ['Al-Qur\'an dan Hadis', 'Akhlak dan budi pekerti', 'Fiqih muamalat'],
            ],
            [
                'name' => 'Budi Santoso, S.Pd.',
                'position' => 'Guru Bahasa Inggris',
                'photo' => 'images/guru/budi-santoso.jpg',
                'description' => 'Membimbing siswa berbahasa Inggris untuk dokumentasi kerja dan '.
                    'komunikasi di industri.',
                'expertise' => ['English for work', 'Speaking', 'Technical writing'],
            ],
            [
                'name' => 'Yuni Kartika, S.Pd.',
                'position' => 'Guru Bimbingan Konseling',
                'photo' => 'images/guru/yuni-kartika.jpg',
                'description' => 'Menjadi tempat siswa bertumbuh: pendampingan belajar, kesiapan karier, '.
                    'dan bekal menghadapi ujian sekolah.',
                'expertise' => ['Bimbingan belajar', 'Karier siswa', 'Konseling'],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Hero background media
    |--------------------------------------------------------------------------
    |
    | The banner is a sequence of slides that rotate on their own. Each entry
    | is one slide and takes a photo OR a clip, never both:
    |
    |   'photo'     bare filename, path from the media base, or a full URL
    |   'video'     same, for a looping clip instead of a still
    |   'poster'    still shown while a clip loads (video slides only)
    |   'duration'  milliseconds this slide stays on screen
    |   'caption'   true for the one slide that carries the school name, the
    |               slogan and the buttons; the rest are plain photos
    |
    | A slide whose file has not been uploaded yet falls back to the green
    | brick texture, so an unfinished set still cycles on its timings.
    |
    */

    'hero' => [
        'slides' => [
            [
                'photo' => 'images/hero/hero1.jpeg',
                'duration' => 3000,
                'caption' => true,
            ],
            ['photo' => 'images/hero/hero2.jpeg', 'duration' => 1000],
            ['photo' => 'images/hero/hero3.jpeg', 'duration' => 1000],

            // The 5 MB hero clip is parked here. Uncomment to put it in the
            // sequence as a fourth slide:
            // ['video' => 'videos/hero/hero-sekolah.mp4', 'duration' => 1000],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Contact details
    |--------------------------------------------------------------------------
    */

    'contact' => [
        'whatsapp' => '0851-8688-0834',
        'whatsapp_link' => 'https://wa.me/6285186880834',
        'phone' => '0851-8688-0834',
        'phone_link' => 'tel:+6285186880834',
        'email' => 'info@smkislamiyahciputat.com',
        'address' => 'Jl. Raya Ciputat, Kab. Tanger, Banten',
        'maps_url' => 'https://www.google.com/maps/search/?api=1&query=SMK+Islamiyah+Ciputat',
    ],

    /*
    |--------------------------------------------------------------------------
    | Social media
    |--------------------------------------------------------------------------
    */

    'socials' => [
        'instagram' => [
            'label' => 'Instagram',
            'url' => 'https://instagram.com/smkislamiyahciputat',
        ],
        'facebook' => [
            'label' => 'Facebook',
            'url' => 'https://facebook.com/smkislamiyahciputat',
        ],
        'youtube' => [
            'label' => 'YouTube',
            'url' => 'https://youtube.com/@smkislamiyahciputat',
        ],
    ],

];
