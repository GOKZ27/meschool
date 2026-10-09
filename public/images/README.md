# Foto untuk website

Taruh file foto di sini, lalu panggil dari view atau `config/school.php`.
Folder ini ada di dalam `public/`, jadi file bisa dipanggil langsung lewat URL
tanpa lewat `asset()`:

```blade
<img src="{{ asset('images/hero/gedung-sekolah.jpg') }}" alt="Gedung sekolah">
```

Atur `APP_URL` di `.env` bila website diakses dari domain sebenarnya.

## Isi tiap folder

| Folder | Untuk | Contoh nama file |
| --- | --- | --- |
| `hero/` | Foto latar hero di halaman depan | `hero1.jpeg` |
| `principal/` | Potret kepala sekolah untuk sambutan di halaman depan | `kepala-sekolah.jpeg` |
| `program/` | Foto tiap program keahlian (TKJ, DKV, dll.) | `program-tkj.jpg` |
| `berita/` | Foto pengumuman, kegiatan, prestasi | `kegiatan-olimpiade.jpg` |
| `galeri/` | Galeri kegiatan di halaman depan | `kegiatan-2026-01.jpg` |
| `kontak/` | Foto lokasi, denah, atau peta | `lokasi-sekolah.jpg` |
| `fasilitas/` | Foto ruang kelas, lab, lapangan | `lab-komputer.jpg` |
| `logo/` | Logo sekolah (jika memakai file, bukan `<x-logo>`) | `logo-sekolah.png` |

Latar hero boleh berupa foto **atau** video, pilih salah satu untuk tiap slide.
Video disimpan terpisah di `public/videos/`, lihat `public/videos/README.md`.

## Tips

- Foto hero paling bagus beresolusi lebar sekitar 1920px dengan orientasi landscape.
- Compres dulu sebelum diunggah supaya halaman tidak berat. Target di bawah
  300 KB per foto hero, 150 KB per foto daftar.
- Pakai nama file yang deskriptif dan huruf kecil, tanpa spasi
  (contoh: `kegiatan-olimpiade-2026.jpg`).
- Kalau nama file fotonya tidak mau diubah, isi `config/school.php`:

  ```php
  'hero' => [
      'slides' => [
          ['photo' => 'images/hero/gedung-sekolah.jpg', 'duration' => 3000, 'caption' => true],
          ['photo' => 'images/hero/kegiatan-1.jpg', 'duration' => 1000],
      ],
  ],
  ```

  Foto hero berputar sendiri: `duration` adalah lama slide tampil dalam
  milidetik, dan hanya satu slide yang diberi `'caption' => true` untuk
  membawa nama sekolah, slogan, dan tombol. Foto yang belum diunggah tetap
  dihitung, hanya tampil sebagai tekstur hijau bata sampai filenya ada.