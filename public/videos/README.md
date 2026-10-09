# Video untuk website

Folder ini berisi video pendek untuk latar (background) halaman depan. Klip
biasanya jauh lebih besar daripada foto, jadi video tidak ikut disimpan di
`public/images/`.

## Isi tiap folder

| Folder | Untuk | Contoh nama file |
| --- | --- | --- |
| `hero/` | Video latar di halaman depan | `gedung-sekolah.mp4` |

## Cara pakai

Foto **atau** video untuk latar, pilih salah satu di `config/school.php`:

```php
'hero' => [
    'photo' => 'images/hero/gedung-sekolah.jpg',
],
```

```php
'hero' => [
    'video' => 'videos/hero/gedung-sekolah.mp4',
    // opsional: gambar yang tampil selagi video dimuat
    'poster' => 'images/hero/gedung-sekolah.jpg',
],
```

Kalau keduanya terisi, yang dipakai adalah video. Kalau file yang ditunjuk
belum ada di folder, halaman tetap aman: latar memakai tekstur hijau bata.

Nilai boleh juga berupa URL penuh (`https://cdn.example.com/hero.mp4`) untuk
video yang disimpan di luar.

## Tips

- Orientation landscape, resolusi sekitar 1920px, durasi 5–15 detik.
- Video loop harus tidak bersuara (tanpa narasi atau musik yang mengganggu),
  karena diputar otomatis dan tidak ada tombol jeda.
- Compres supaya halaman tidak berat. Target di bawah 3 MB untuk video hero,
  atau pakai beberapa versi (misalnya `.mp4` dan `.webm`) sekaligus.
- Nama file deskriptif dan huruf kecil, tanpa spasi.