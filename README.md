# be-portofolio

API Laravel + MySQL untuk website portofolio. Folder ini berisi file-file
khusus portofolio; kerangka Laravel-nya dibuat lewat Composer (langkah 1).

## Setup pertama kali (Windows, CMD)

```bat
cd portofolio
ren be-portofolio be-portofolio-src
composer create-project laravel/laravel be-portofolio
cd be-portofolio
php artisan install:api
```
Saat `install:api` bertanya mau menjalankan migration, jawab **no** (database belum disiapkan).

Lalu salin file portofolio ke atas kerangka Laravel (menimpa `routes/api.php`, `config/cors.php`, `DatabaseSeeder.php`):

```bat
xcopy ..\be-portofolio-src . /E /Y
rmdir /S /Q ..\be-portofolio-src
```

## Database

1. Buat database kosong bernama `portofolio` di MySQL (phpMyAdmin / HeidiSQL / `CREATE DATABASE portofolio;`).
2. Samakan isi `.env` dengan `.env.portofolio.example` (bagian DB dan `FRONTEND_URL`).
3. Jalankan:

```bat
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Cek: buka http://localhost:8000/api/portfolio, harus keluar JSON berisi `profile`, `projects`, dst.

## Mengubah isi portofolio

Edit `database/seeders/data/portfolio.json`, lalu:

```bat
php artisan db:seed --class=PortfolioSeeder
```

Seeder ini menghapus lalu mengisi ulang semua data kecuali pesan kontak.
Jaga agar isinya sama dengan `fe-portofolio/src/data/fallback.js`.

Gambar: taruh file di `storage/app/public/projects/...`, lalu isi `thumbnail_url` / `images[].url`
dengan path relatif seperti `projects/dompetkita/cover.webp`.

## Endpoint

| Method | URL | Keterangan |
|---|---|---|
| GET | /api/portfolio | Semua data halaman utama |
| GET | /api/projects?category=erp\|web\|mobile | Daftar proyek published |
| GET | /api/projects/{slug} | Detail proyek + gambar |
| POST | /api/contact | Kirim pesan (maks 5/menit per IP) |

Kontrak lengkap: `../docs/api-contract.md`.

## Tabel

`profiles` (1 baris), `skills`, `projects`, `project_images`, `experiences`,
`educations`, `certifications`, `contact_messages`.

Pesan kontak yang masuk bisa dilihat dengan `php artisan tinker` →
`App\Models\ContactMessage::latest()->get();`
