<?php

use App\Http\Controllers\BkkController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/kontak', 'kontak')->name('kontak');

// The Maps menu entry points straight at Google Maps rather than an in-app page.
Route::redirect('/maps', config('school.contact.maps_url'))->name('maps');

/*
|--------------------------------------------------------------------------
| Search-engine plumbing
|--------------------------------------------------------------------------
|
| The sitemap is generated and robots.txt points at it. Both are routes rather
| than static files so their URLs follow APP_URL on any deployment — the app
| may be served from a subfolder such as /meschool.
|
*/

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('/robots.txt', function () {
    return response(
        "User-agent: *\nAllow: /\nDisallow: /pencarian\n\nSitemap: ".url('/sitemap.xml')."\n",
        200,
        ['Content-Type' => 'text/plain; charset=UTF-8'],
    );
})->name('robots');

/*
|--------------------------------------------------------------------------
| Berita
|--------------------------------------------------------------------------
|
| The archive is the landing page, the three legacy child URLs stay
| registered so old bookmarks keep working (they open the same archive
| already filtered), and the article route comes last so /berita/pengumuman
| is read as a category rather than as a slug.
|
*/

Route::get('/berita', [NewsController::class, 'index'])->name('berita');

foreach (['pengumuman', 'kegiatan', 'prestasi'] as $child) {
    Route::get('/berita/'.$child, [NewsController::class, 'index'])
        ->defaults('category', $child)
        ->name('berita.'.$child);
}

Route::get('/berita/{slug}', [NewsController::class, 'show'])
    ->where('slug', '[a-z0-9\-]+')
    ->name('berita.show');

/*
|--------------------------------------------------------------------------
| BKK — Bursa Kerja Khusus
|--------------------------------------------------------------------------
|
| The archive of job listings for students and alumni. The job route comes
| after the placeholder block at the bottom, so /bkk/lowongan and
| /bkk/tracer-study keep reading as their legacy pages rather than as a slug.
|
*/

Route::get('/bkk', [BkkController::class, 'index'])->name('bkk');

/*
|--------------------------------------------------------------------------
| Placeholder destinations
|--------------------------------------------------------------------------
|
| One stub route per navbar entry so no menu item leads to a 404. Swap an
| individual entry for a real controller as that section gets built out.
|
*/

$stub = fn (string $title, string $topic) => [
    'title' => $title,
    'description' => "Halaman ini disiapkan untuk memuat informasi lengkap {$topic}, mulai dari profil, ".
        'kegiatan, hingga pengumuman terbaru. Silakan kembali beberapa saat lagi.',
];

$placeholders = [
    // Legacy child URLs that are no longer in the bar but stay registered so
    // old bookmarks and links keep working.
    'bkk.lowongan' => ['/bkk/lowongan', 'Informasi Lowongan Pekerjaan', 'informasi lowongan pekerjaan untuk alumni SMK Islamiyah Ciputat'],
    'bkk.tracer-study' => ['/bkk/tracer-study', 'Tracer Study', 'data dan hasil tracer study alumni SMK Islamiyah Ciputat'],

    'spmb.tentang-kami' => ['/spmb/tentang-kami', 'Tentang Kami', 'sejarah, visi, misi, dan profil singkat SMK Islamiyah Ciputat'],
    'spmb.informasi-sekolah' => ['/spmb/informasi-sekolah', 'Informasi Sekolah', 'informasi umum sekolah, mulai dari profil sekolah, fasilitas, hingga jadwal pendaftaran'],

    'search' => ['/pencarian', 'Pencarian', 'hasil pencarian'],
];

foreach ($placeholders as $name => [$uri, $title, $topic]) {
    Route::view($uri, 'placeholder', $stub($title, $topic))->name($name);
}

/*
|--------------------------------------------------------------------------
| Section landing pages
|--------------------------------------------------------------------------
|
| The flat navbar entries and the "Program Keahlian" dropdown all land on
| real copy-driven pages: SPMB, Tenaga Kependidikan, and one programme page per
| slug in config('school.program_catalog'). Each programme route is a literal
| URI with a 'program' default, so an unknown slug is a 404 by construction —
| there is simply no route for it.
|
*/

Route::view('/spmb', 'spmb')->name('spmb');
Route::view('/tenaga-kependidikan', 'tenaga-kependidikan')->name('tenaga-kependidikan');

foreach (config('school.program_catalog.items', []) as $slug => $info) {
    Route::get('/program-keahlian/'.$slug, [ProgramController::class, 'show'])
        ->defaults('program', $slug)
        ->name('program.'.$slug);
}

/*
| BKK job listings must not swallow the legacy child pages above, so the slug
| route is registered last — just like /berita/{slug}.
*/

Route::get('/bkk/{slug}', [BkkController::class, 'show'])
    ->where('slug', '[a-z0-9\-]+')
    ->name('bkk.show');
