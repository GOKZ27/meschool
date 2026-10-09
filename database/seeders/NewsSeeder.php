<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Seeder;

/**
 * The working copy of the news archive.
 *
 * There is no CMS behind this site yet, so the articles below are the seed
 * content: swap a row for a real story and every block on the home page,
 * the /berita archive and the article page follow. Rows are upserted on
 * slug, so re-seeding updates in place instead of duplicating.
 *
 * Media is deliberately left as filenames under public/images/berita/ and
 * public/videos/berita/ — drop the real files in and the placeholder tiles
 * turn into photos and playable clips, no code change needed.
 */
class NewsSeeder extends Seeder
{
    /**
     * @return array<int, array<string, mixed>>
     */
    private function articles(): array
    {
        return [
            [
                'slug' => 'ppdb-2027-resmi-dibuka',
                'title' => 'PPDB 2027 Resmi Dibuka, Tiga Program Keahlian Dibuka untuk Pendaftar Baru',
                'excerpt' => 'Pendaftaran siswa baru tahun pelajaran 2027/2028 dibuka mulai awal Januari, '
                    .'dengan jalur zonasi, prestasi, dan afirmasi.',
                'category' => 'pengumuman',
                'author' => 'Humas SMK Islamiyah Ciputat',
                'views' => 1840,
                'published_at' => '2026-10-02 08:00:00',
                'is_featured' => true,
                'is_picked' => true,
                'body' => implode("\n\n", [
                    'Penerimaan Peserta Didik Baru (PPDB) tahun pelajaran 2027/2028 resmi dibuka. '
                        .'Pendaftaran dilakukan secara daring melalui laman sekolah mulai 4 Januari 2027 '
                        .'sampai 20 Februari 2027, dilayani langsung di ruang panitia pada hari kerja '
                        .'pukul 08.00 sampai 15.00 WIB.',

                    'Tiga program keahlian dibuka pada tahun ini: Animasi, Desain Komunikasi Visual, '
                        .'serta Teknik Jaringan Komputer dan Telekomunikasi. Kuota tiap program keahlian '
                        .'dibagi ke dalam tiga jalur pendaftaran — zonasi berdasarkan domisili, prestasi '
                        .'akademik maupun non-akademik, dan afirmasi bagi keluarga prasejahtera.',

                    'Calon siswa cukup membawa fotokopi rapor semester terakhir, akta kelahiran, kartu '
                        .'keluarga, dan pas foto 3x4. Berkas asli dibawa pada saat daftar ulang. Panitia '
                        .'menegaskan bahwa seluruh tahapan tidak dipungut biaya apa pun.',
                ]),
            ],
            [
                'slug' => 'juara-web-design-se-dki-jakarta',
                'title' => 'Siswa PPLG Raih Juara 1 Lomba Web Design tingkat DKI Jakarta',
                'excerpt' => 'Tim tiga siswa kelas XI PPLG mengungguli 42 peserta lain dengan situs '
                    .'katalog UMKM berbasis aksesibilitas.',
                'category' => 'prestasi',
                'author' => 'Tim Redaksi Sekolah',
                'views' => 1420,
                'published_at' => '2026-09-24 10:00:00',
                'is_picked' => true,
                'body' => implode("\n\n", [
                    'Lomba Web Design tingkat DKI Jakarta yang digelar di Jakarta Pusat menobatkan tim '
                        .'tiga siswa SMK Islamiyah Ciputat dari program keahlian PPLG sebagai juara pertama. '
                        .'Mereka mengungguli 42 peserta dari sekolah-sekolah se-Indonesia bagian barat.',

                    'Karya mereka berupa katalog UMKM kelurahan dengan penekanan pada aksesibilitas: '
                        .'kontras warna lolos WCAG AA, navigasi penuh lewat papan ketik, dan waktu muat '
                        .'di bawah dua detik pada jaringan 4G. Penilai menyoroti keputusan tim untuk '
                        .'menulis ulang seluruh gambar ke format modern tanpa mengurangi ketajaman.',

                    'Persiapan berlangsung sekitar enam minggu di luar jam pelajaran, didampingi guru '
                        .'PPLG. Ketua tim menyebut kendala terbesar bukan kodenya, melainkan menjaga '
                        .'agar desain tetap sederhana saat banyak ide bermunculan.',
                ]),
            ],
            [
                'slug' => 'masa-orientasi-siswa-angkatan-2026',
                'title' => 'Masa Orientasi Siswa Angkatan 2026 Digelar Tiga Hari di Aula Sekolah',
                'excerpt' => '428 siswa baru mengikuti pengenalan lingkungan sekolah, kelas, dan '
                    .'tata tertib sebelum pelajaran dimulai.',
                'category' => 'kegiatan',
                'author' => 'Humas SMK Islamiyah Ciputat',
                'views' => 980,
                'published_at' => '2026-09-18 09:00:00',
                'is_picked' => true,
                'video' => 'videos/berita/masa-orientasi-siswa.mp4',
                'gallery' => ['mos-2026-upacara-pembukaan.jpg', 'mos-2026-kelas-dasar.jpg'],
                'body' => implode("\n\n", [
                    'Masa Orientasi Siswa angkatan 2026 berlangsung selama tiga hari di aula dan '
                        .'ruang-ruang kelas. Kegiatan dibuka dengan upacara pembukaan, dilanjutkan '
                        .'pengenalan program keahlian masing-masing kelas oleh wali kelas.',

                    'Selain materi tata tertib dan budaya sekolah, siswa baru juga diajak berkeliling '
                        .'mengunjungi bengkel praktik, laboratorium komputer, dan perpustakaan. Hari '
                        .'terakhir ditutup dengan permainan tim yang disusun oleh pengurus OSIS.',

                    'Panitia menyiapkan pos kesehatan sepanjang kegiatan dan membagi peserta ke dalam '
                        .'kelompok kecil agar setiap siswa dikenal pengawasnya sejak hari pertama.',
                ]),
            ],
            [
                'slug' => 'praktik-kerja-lapangan-45-siswa-berangkat',
                'title' => '45 Siswa PPLG dan TKJT Berangkat Praktik Kerja Lapangan ke Industri',
                'excerpt' => 'Penempatan PKL semester ganjil mencakup perusahaan telekomunikasi, '
                    .'studio animasi, dan lembaga keuangan.',
                'category' => 'akademik',
                'author' => 'Tim Redaksi Sekolah',
                'views' => 870,
                'published_at' => '2026-09-10 08:30:00',
                'is_picked' => true,
                'body' => implode("\n\n", [
                    'Sebanyak 45 siswa program keahlian PPLG dan TKJT berangkat menjalani Praktik Kerja '
                        .'Lapangan selama enam bulan. Penempatan dilakukan di perusahaan telekomunikasi, '
                        .'studio animasi, dan beberapa lembaga keuangan di wilayah Tangerang Selatan.',

                    'Sebelum berangkat, siswa mengikuti pembekalan selama dua hari: penulisan laporan, '
                        .'etika kerja, dan keselamatan kerja. Setiap siswa dibimbing satu pembimbing '
                        .'industri dan satu guru pendamping dari sekolah.',

                    'Laporan PKL menjadi salah satu syarat kelulusan. Nilai akhir dihitung dari '
                        .'catatan harian, penilaian pembimbing industri, dan hasil presentasi akhir '
                        .'yang dijadwalkan pada akhir semester.',
                ]),
            ],
            [
                'slug' => 'jadwal-ujian-semester-ganjil-2026',
                'title' => 'Jadwal Ujian Semester Ganjil 2026/2027 Diumumkan, Dimulai 16 November',
                'excerpt' => 'Ujian berlangsung sembilan hari, dilanjutkan ujian praktik keahlian '
                    .'untuk kelas XII pada awal Desember.',
                'category' => 'akademik',
                'author' => 'Humas SMK Islamiyah Ciputat',
                'views' => 640,
                'published_at' => '2026-09-02 07:30:00',
                'is_picked' => true,
                'body' => implode("\n\n", [
                    'Ujian semester ganjil tahun pelajaran 2026/2027 dijadwalkan berlangsung mulai '
                        .'16 November sampai 24 November 2026. Soal disusun oleh dewan guru tiap mata '
                        .'pelajaran dan diacak ulang untuk tiap ruang.',

                    'Ujian praktik keahlian kelas XII digelar terpisah pada 1 sampai 4 Desember 2026 '
                        .'di bengkel dan laboratorium masing-masing program keahlian. Kriteria kelulusan '
                        .'praktik mengacu pada standar kompetensi keahlian yang berlaku.',

                    'Siswa diminta hadir 15 menit sebelum ujian dimulai dengan membawa kartu peserta '
                        .'dan alat tulis sendiri. Jadwal lengkap per ruang dipasang di papan informasi '
                        .'dan dibagikan melalui wali kelas.',
                ]),
            ],
            [
                'slug' => 'lks-tkj-tiga-siswa-lolos-provinsi',
                'title' => 'Tiga Siswa TKJT Lolos ke Tingkat Provinsi pada Kompetisi LKS 2026',
                'excerpt' => 'Babak penyisihan diisi uji konfigurasi jaringan, perakitan perangkat, '
                    .'dan penanganan gangguan.',
                'category' => 'prestasi',
                'author' => 'Tim Redaksi Sekolah',
                'views' => 1130,
                'published_at' => '2026-08-26 11:00:00',
                'is_picked' => true,
                'video' => 'videos/berita/lks-tkj-2026.mp4',
                'gallery' => ['lks-2026-babak-penyisihan.jpg', 'lks-2026-tim-tkjt.jpg'],
                'body' => implode("\n\n", [
                    'Kompetisi Siswa Nasional bidang Teknik Komputer dan Jaringan kembali mengirimkan '
                        .'tiga siswa SMK Islamiyah Ciputat ke babak provinsi. Ketiganya lolos setelah '
                        .'menyelesaikan seluruh uji dalam babak penyisihan tingkat kota.',

                    'Materi lomba meliputi perakitan perangkat, konfigurasi router dan switch, '
                        .'pembuatan skema jaringan, serta penanganan gangguan yang dipasang panitia. '
                        .'Waktu pengerjaan tiap sesi dibatasi 90 menit dengan penilaian langsung di '
                        .'akhir sesi.',

                    'Tim berlatih selama sebulan di laboratorium jaringan pada sore hari. Babak '
                        .'provinsi dijadwalkan Oktober 2026, dan persiapan akan difokuskan pada '
                        .'konfigurasi nirkabel dan keamanan jaringan.',
                ]),
            ],
            [
                'slug' => 'kerja-sama-industri-tiga-perusahaan',
                'title' => 'Sekolah Tandatangani Kerja Sama dengan Tiga Perusahaan untuk Dukung PKL',
                'excerpt' => 'Nota kesepahaman memastikan kuota penempatan siswa dan pendampingan '
                    .'instruktur industri setiap semester.',
                'category' => 'akademik',
                'author' => 'Humas SMK Islamiyah Ciputat',
                'views' => 760,
                'published_at' => '2026-08-15 13:00:00',
                'body' => implode("\n\n", [
                    'Nota kesepahaman kerja sama ditandatangani bersama tiga perusahaan yang selama '
                        .'ini menjadi mitra penempatan siswa. Perjanjian memuat komitmen kuota siswa '
                        .'per tahun dan jadwal kunjungan guru ke tempat kerja.',

                    'Selain penempatan siswa, kesepakatan juga mencakup hadirnya instruktur industri '
                        .'ke sekolah minimal dua kali dalam satu semester untuk mengisi sesi praktik '
                        .'dan meninjau kurikulum.',

                    'Kerja sama ini diharapkan memperpendek waktu tunggu siswa yang belum memiliki '
                        .'penempatan sejak awal semester, sekaligus menjaga kualitas laporan praktik '
                        .'kerja lapangan yang dikerjakan di tempat masing-masing.',
                ]),
            ],
            [
                'slug' => 'video-profil-sekolah-dirilis',
                'title' => 'Video Profil SMK Islamiyah Ciputat Dirilis, Memuat Fasilitas dan Kegiatan Harian',
                'excerpt' => 'Video berdurasi tiga menit memperkenalkan bengkel praktik, laboratorium, '
                    .'serta rutinitas siswa dari pagi sampai pulang.',
                'category' => 'kegiatan',
                'author' => 'Tim Redaksi Sekolah',
                'views' => 1560,
                'published_at' => '2026-08-05 09:00:00',
                'video' => 'videos/hero/hero-sekolah.mp4',
                'gallery' => ['video-profil-bengkel-praktik.jpg', 'video-profil-lab-komputer.jpg'],
                'body' => implode("\n\n", [
                    'Video profil sekolah kini sudah bisa ditonton di halaman ini. Rekaman berdurasi '
                        .'sekitar tiga menit memperkenalkan gedung sekolah, bengkel praktik tiap program '
                        .'keahlian, laboratorium komputer, dan perpustakaan.',

                    'Bagian kedua video menampilkan kegiatan harian: apel pagi, jam belajar, praktik '
                        .'di bengkel, dan kegiatan ekstrakurikuler pada sore hari. Semua rekaman '
                        .'diambil pada hari biasa, tanpa pengaturan khusus, agar calon siswa melihat '
                        .'keadaan sekolah sebagaimana adanya.',

                    'Rekaman versi penuh juga disimpan sekolah dan dapat diputar pada kegiatan penerimaan '
                        .'peserta didik baru.',
                ]),
            ],
            [
                'slug' => 'bursa-kerja-khusus-oktober-2026',
                'title' => 'Bursa Kerja Khusus Digelar Oktober, Delapan Perusahaan Dijadwalkan Hadir',
                'excerpt' => 'Kegiatan terbuka untuk alumni angkatan 2025 dan 2026 dengan pendaftaran '
                    .'daring sebelum hari pelaksanaan.',
                'category' => 'bkk',
                'author' => 'Humas SMK Islamiyah Ciputat',
                'views' => 520,
                'published_at' => '2026-07-28 10:00:00',
                'body' => implode("\n\n", [
                    'Bursa Kerja Khusus kembali digelar di aula sekolah pada 17 Oktober 2026. Sebanyak '
                        .'delapan perusahaan dari bidang manufaktur, jasa, dan teknologi informasi '
                        .'dijadwalkan membuka pendaftaran pada hari yang sama.',

                    'Kegiatan terbuka bagi alumni angkatan 2025 dan 2026 serta siswa kelas XII yang '
                        .'sudah menyelesaikan praktik kerja lapangan. Pendaftaran dilakukan daring '
                        .'melalui halaman BKK paling lambat tiga hari sebelum acara.',

                    'Peserta diimbau membawa beberapa lembar fotokopi CV, rapor terakhir, dan alat '
                        .'tulis. Beberapa perusahaan menggelar wawancara langsung di tempat, sehingga '
                        .'kandidat berpeluang diterima pada hari yang sama.',
                ]),
            ],
            [
                'slug' => 'tracer-study-2026-kepuasan-92',
                'title' => 'Hasil Tracer Study Alumni 2026: 92 Persen Sudah Bekerja atau Melanjutkan Studi',
                'excerpt' => 'Survei menemukan sebagian besar alumni bekerja sesuai bidang keahlian '
                    .'dalam tempo kurang dari tiga bulan setelah lulus.',
                'category' => 'bkk',
                'author' => 'Tim Redaksi Sekolah',
                'views' => 430,
                'published_at' => '2026-07-12 08:00:00',
                'body' => implode("\n\n", [
                    'Survei tracer study terhadap alumni angkatan 2026 menemukan 92 persen responden '
                        .'sudah bekerja atau melanjutkan studi pada saat survei dijalankan, tiga bulan '
                        .'setelah kelulusan.',

                    'Di antara yang bekerja, sekitar tiga perempatnya bekerja pada bidang yang sesuai '
                        .'dengan program keahlian yang pernah ditempuh. Alasan paling sering disebut '
                        .'adalah pengalaman praktik kerja lapangan yang memudahkan proses wawancara.',

                    'Sekolah akan memakai hasil ini untuk menyesuaikan materi pembekalan karier dan '
                        .'memperluas mitra industri, khususnya pada program keahlian yang penempatannya '
                        .'masih terbatas.',
                ]),
            ],
            [
                'slug' => 'pembagian-rapor-akhir-semester-ganjil',
                'title' => 'Pembagian Rapor Semester Ganjil Dilaksanakan 19 Desember 2026',
                'excerpt' => 'Rapat wali kelas dijadwalkan pagi hari, dilanjutkan pertemuan individual '
                    .'dengan guru mapel pukul 09.00.',
                'category' => 'pengumuman',
                'author' => 'Humas SMK Islamiyah Ciputat',
                'views' => 610,
                'published_at' => '2026-06-20 07:00:00',
                'body' => implode("\n\n", [
                    'Pembagian laporan perkembangan anak didik semester ganjil dilaksanakan pada '
                        .'19 Desember 2026. Kegiatan dimulai pukul 07.30 WIB dengan rapat wali kelas, '
                        .'lalu dilanjutkan pertemuan individual dengan guru mata pelajaran.',

                    'Wali murid yang berhalangan hadir dapat mewakilkan dengan surat kuasa beserta '
                        .'kartu keluarga, atau mengambil rapor pada hari kerja berikutnya di ruang '
                        .'tata usaha.',

                    'Selain nilai akademik, rapor memuat catatan perkembangan sikap dan kehadiran, '
                        .'serta rekomendasi kegiatan yang disarankan guru untuk semester berikutnya.',
                ]),
            ],
            [
                'slug' => 'tim-futsal-juara-2-turnamen-ciputat',
                'title' => 'Tim Futsal Sekolah Raih Juara 2 pada Turnamen Antar-SMK se-Ciputat',
                'excerpt' => 'Kekalahan di babak final hanya berselisih satu gol setelah pertandingan '
                    .'berakhir imbang sampai waktu habis.',
                'category' => 'prestasi',
                'author' => 'Tim Redaksi Sekolah',
                'views' => 890,
                'published_at' => '2026-05-30 16:00:00',
                'body' => implode("\n\n", [
                    'Tim futsal SMK Islamiyah Ciputat menutup Turnamen Antar-SMK se-Ciputat sebagai '
                        .'juara kedua. Pertandingan final berlangsung ketat dan berakhir imbang sampai '
                        .'waktu normal selesai.',

                    'Tim sempat tertinggal lebih dulu di awal babak kedua, lalu menyamakan kedudukan '
                        .'lewat serangan balik cepat. Sayangnya, satu gol di menit-menit akhir waktu '
                        .'tambahan menentukan hasil akhir.',

                    'Prestasi ini menjadi bekal tim untuk persiapan kejuaraan tingkat kota pada '
                        .'semester berikutnya, dengan tambahan latihan tiga kali seminggu.',
                ]),
            ],
            [
                'slug' => 'pameran-karya-dkv-aula-sekolah',
                'title' => 'Pameran Karya DKV Tampilkan 60 Karya Siswa di Aula Sekolah',
                'excerpt' => 'Poster, kemasan, dan identitas visual dipamerkan selama tiga hari dan '
                    .'terbuka untuk pengunjung umum.',
                'category' => 'kegiatan',
                'author' => 'Tim Redaksi Sekolah',
                'views' => 540,
                'published_at' => '2026-05-12 10:00:00',
                'gallery' => ['pameran-dkv-poster.jpg', 'pameran-dkv-kemasan.jpg'],
                'body' => implode("\n\n", [
                    'Pameran karya program keahlian Desain Komunikasi Visual digelar selama tiga hari '
                        .'di aula sekolah. Enam puluh karya dipamerkan, mulai dari poster kampanye, '
                        .'desain kemasan, sampai identitas visual lembaga fiktif hasil kerja kelas XI.',

                    'Setiap karya disertai kartu keterangan berisi proses kerja, dari riset awal, '
                        .'sketsa, sampai versi akhir. Pengunjung dapat memberi penilaian melalui kertas '
                        .'yang disediakan di pintu masuk.',

                    'Karya dengan penilaian tertinggi akan mewakili sekolah pada lomba desain tingkat '
                        .'kota pada bulan berikutnya.',
                ]),
            ],
            [
                'slug' => 'open-house-spmb-2026',
                'title' => 'Open House SPMB 2026 Sambut 300 Calon Siswa dan Orang Tua',
                'excerpt' => 'Sesi tanya jawab bersama guru dan siswa berlangsung di aula serta '
                    .'kunjungan langsung ke bengkel praktik.',
                'category' => 'spmb',
                'author' => 'Humas SMK Islamiyah Ciputat',
                'views' => 720,
                'published_at' => '2026-03-14 09:00:00',
                'body' => implode("\n\n", [
                    'Open house penerimaan peserta didik baru 2026 diikuti sekitar 300 calon siswa '
                        .'beserta orang tua. Kegiatan dibuka dengan paparan profil sekolah dan '
                        .'penjelasan tiga program keahlian yang dibuka.',

                    'Sesi tanya jawab berlangsung selama hampir dua jam, memunculkan pertanyaan soal '
                        .'biaya, peluang kerja setelah lulus, serta fasilitas bengkel yang tersedia '
                        .'bagi siswa kelas X.',

                    'Setelah sesi di aula, pengunjung diajak berkeliling ke bengkel praktik, lab '
                        .'komputer, dan ruang kelas. Rangkaian kegiatan ditutup dengan pendaftaran '
                        .'awal di meja panitia.',
                ]),
            ],
            [
                'slug' => 'beasiswa-prestasi-yayasan-islamiyah',
                'title' => 'Beasiswa Prestasi Yayasan Islamiyah Dibuka untuk 25 Siswa Berprestasi',
                'excerpt' => 'Pembebasan biaya pendidikan diberikan pada siswa dengan nilai rapor dan '
                    .'keaktifan kegiatan tertentu.',
                'category' => 'pengumuman',
                'author' => 'Humas SMK Islamiyah Ciputat',
                'views' => 660,
                'published_at' => '2026-02-20 08:00:00',
                'gallery' => ['beasiswa-2026-penyerahan.jpg', 'beasiswa-2026-siswa-penerima.jpg'],
                'body' => implode("\n\n", [
                    'Yayasan Islamiyah kembali membuka beasiswa prestasi tahun 2026 untuk 25 siswa. '
                        .'Beasiswa berupa pembebasan sebagian biaya pendidikan selama satu tahun '
                        .'pelajaran.',

                    'Syaratnya mencakup nilai rapor semester terakhir di atas rata-rata, kehadiran '
                        .'minimal 95 persen, serta keterlibatan dalam kegiatan sekolah atau lomba '
                        .'tingkat kota ke atas.',

                    'Pendaftaran ditutup pada 10 Maret 2026. Berkas diserahkan ke ruang tata usaha '
                        .'dan hasil seleksi diumumkan melalui papan informasi serta wali kelas.',
                ]),
            ],
            [
                'slug' => 'wisuda-angkatan-2026-luluskan-212-siswa',
                'title' => 'Wisuda Angkatan 2026 Luluskan 212 Siswa di Gedung Serbaguna Ciputat',
                'excerpt' => 'Empat siswa meraih predikat terbaik dengan nilai akhir di atas 90, '
                    .'didampingi orang tua masing-masing.',
                'category' => 'akademik',
                'author' => 'Tim Redaksi Sekolah',
                'views' => 1290,
                'published_at' => '2026-01-24 14:00:00',
                'gallery' => ['wisuda-2026-upacara.jpg', 'wisuda-2026-siswa-terbaik.jpg'],
                'body' => implode("\n\n", [
                    'Wisuda angkatan 2026 dilaksanakan di gedung serbaguna dengan dihadiri orang tua '
                        .'wali siswa. Sebanyak 212 siswa menerima tanda kelulusan setelah menyelesaikan '
                        .'seluruh tahapan ujian.',

                    'Empat siswa meraih predikat terbaik dengan nilai akhir di atas 90, berasal dari '
                        .'program keahlian PPLG, DKV, dan TKJT. Mereka naik panggung didampingi orang '
                        .'tua masing-masing.',

                    'Dalam sambutannya, sekolah menekankan bahwa kelulusan bukan akhir dari belajar, '
                        .'melainkan perpindahan tempat belajar ke dunia kerja atau pendidikan lanjutan.',
                ]),
            ],
            [
                'slug' => 'ppdb-2026-tahap-pendaftaran-daring',
                'title' => 'PPDB 2026 Tahap Pendaftaran Daring Ditutup, 1.240 Berkas Masuk',
                'excerpt' => 'Jumlah pendaftar meningkat dibanding tahun sebelumnya, terbanyak pada '
                    .'program keahlian PPLG.',
                'category' => 'spmb',
                'author' => 'Humas SMK Islamiyah Ciputat',
                'views' => 830,
                'published_at' => '2025-05-06 15:00:00',
                'body' => implode("\n\n", [
                    'Tahap pendaftaran daring penerimaan peserta didik baru 2026 ditutup pada 30 Mei '
                        .'2025. Panitia mencatat 1.240 bermasuk, meningkat dibanding tahun sebelumnya.',

                    'Program keahlian PPLG kembali menjadi pilihan terbanyak, disusul TKJT dan DKV. '
                        .'Seluruh berkas telah diverifikasi dan hasil seleksi diumumkan melalui laman '
                        .'sekolah serta papan informasi.',

                    'Daftar ulang dilayani selama tiga hari kerja. Panitia mengingatkan agar calon '
                        .'siswa membawa berkas asli sesuai daftar yang tertera pada pengumuman.',
                ]),
            ],
            [
                'slug' => 'bakti-sosial-ramadhan-1447h',
                'title' => 'Bakti Sosial Ramadhan 1447H Salurkan 300 Paket untuk Warga Sekitar',
                'excerpt' => 'Kegiatan gabungan siswa, guru, dan komite sekolah menyasar tiga '
                    .'kelurahan di sekitar lingkungan sekolah.',
                'category' => 'kegiatan',
                'author' => 'Tim Redaksi Sekolah',
                'views' => 470,
                'published_at' => '2025-03-05 11:00:00',
                'gallery' => ['bakti-sosial-ramadhan-2025.jpg', 'bakti-sosial-ramadhan-paket.jpg'],
                'body' => implode("\n\n", [
                    'Bakti sosial bulan Ramadhan kembali digelar dengan menyalurkan 300 paket sembako '
                        .'untuk warga di sekitar lingkungan sekolah. Kegiatan melibatkan pengurus OSIS, '
                        .'perwakilan tiap kelas, dan guru pendamping.',

                    'Paket dibagikan ke tiga kelurahan dengan pendataan bersama RT dan RW setempat '
                        .'agar bantuan tepat sasaran. Sebagian paket juga diserahkan kepada petugas '
                        .'kebersihan dan penjaga sekolah.',

                    'Dana kegiatan berasal dari infak siswa yang dikumpulkan sepanjang dua pekan '
                        .'sebelumnya, dilengkapi sumbangan dari komite sekolah.',
                ]),
            ],
            [
                'slug' => 'renovasi-lab-komputer-selesai',
                'title' => 'Renovasi Laboratorium Komputer Selesai, 40 Unit PC Siap Dipakai',
                'excerpt' => 'Pekerjaan selama dua bulan menambah jaringan baru dan mengganti meja '
                    .'praktik di dua ruang laboratorium.',
                'category' => 'kegiatan',
                'author' => 'Humas SMK Islamiyah Ciputat',
                'views' => 500,
                'published_at' => '2024-12-18 09:00:00',
                'body' => implode("\n\n", [
                    'Renovasi laboratorium komputer yang berlangsung selama dua bulan telah selesai. '
                        .'Pekerjaan meliputi penambahan titik jaringan, penggantian meja praktik, dan '
                        .'perbaikan instalasi listrik di dua ruang laboratorium.',

                    'Sebanyak 40 unit komputer kembali terpasang dan siap dipakai untuk kegiatan '
                        .'belajar maupun persiapan lomba. Satu ruang khusus disediakan untuk kelas XII '
                        .'yang mengerjakan proyek akhir.',

                    'Siswa kembali memakai laboratorium sejak pekan pertama masuk setelah libur '
                        .'akhir tahun, dengan jadwal per kelas yang dipasang di pintu ruang.',
                ]),
            ],
            [
                'slug' => 'akreditasi-a-program-keahlian-pplg',
                'title' => 'Program Keahlian PPLG Raih Akreditasi A dari BAN-S/M',
                'excerpt' => 'Penilaian menyoroti fasilitas praktik, kerja sama industri, dan '
                    .'capaian lulusan pada tiga tahun terakhir.',
                'category' => 'akademik',
                'author' => 'Tim Redaksi Sekolah',
                'views' => 940,
                'published_at' => '2024-11-10 10:00:00',
                'body' => implode("\n\n", [
                    'Program keahlian Pengembangan dan Pemeliharaan Aplikasi serta Gim memperoleh '
                        .'akreditasi A dari Badan Akreditasi Nasional Sekolah/Madrasah hasil '
                        .'penilaian akhir Oktober 2024.',

                    'Aspek yang dinilai meliputi kurikulum, kualifikasi guru, fasilitas praktik, '
                        .'kerja sama dengan industri, serta capaian lulusan selama tiga tahun '
                        .'terakhir.',

                    'Capaian ini menjadi dasar penyusunan rencana kerja dua tahun ke depan, antara '
                        .'lain penambahan perangkat pengujian aplikasi dan pembukaan kelas industri '
                        .'bersama mitra.',
                ]),
            ],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->articles() as $article) {
            News::updateOrCreate(['slug' => $article['slug']], $article + [
                'excerpt' => null,
                'image' => null,
                'gallery' => null,
                'video' => null,
                'author' => 'Humas SMK Islamiyah Ciputat',
                'is_featured' => false,
                'is_picked' => false,
            ]);
        }
    }
}
