<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

/**
 * Contoh proyek yang terisi LENGKAP, diambil dari CV David.
 * Gunanya untuk melihat apakah susunan field proyek (ringkasan, konten,
 * yang saya kerjakan, teknologi, tautan) sudah pas saat tampil di kartu,
 * modal, dan halaman detail.
 *
 *   php artisan db:seed --class=ContohProyekSeeder
 *
 * Aman dijalankan berulang: memakai slug sebagai kunci (updateOrCreate),
 * dan TIDAK menghapus proyek lain.
 */
class ContohProyekSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->projects() as $i => $data) {
            Project::updateOrCreate(
                ['slug' => $data['slug']],
                [...$data, 'status' => 'published', 'sort_order' => 100 + $i]
            );
        }

        $this->command?->info('Contoh proyek EduVision & Web Administrasi Asrama tersimpan.');
    }

    private function projects(): array
    {
        return [
            [
                'slug' => 'eduvision-deteksi-buah',
                'title' => 'EduVision Deteksi Buah',
                'summary' => 'Aplikasi web ramah anak yang mengenali jenis buah dari gambar menggunakan model klasifikasi TensorFlow.',
                'category' => 'web',
                'role' => 'Fullstack Developer',
                'year' => 2025,
                'stack' => ['Python', 'TensorFlow', 'Flask', 'Tailwind CSS'],
                'repo_url' => 'https://github.com/deviansky/EduVision_FruitDetector',
                'demo_url' => null,
                'thumbnail_path' => null,
                'is_featured' => false,
                'is_confidential' => false,
                'description' => <<<'HTML'
<h2>Gambaran proyek</h2>
<p>EduVision adalah aplikasi web untuk anak-anak yang mengenali jenis buah dari gambar. Model machine learning dijalankan di server, lalu hasilnya ditampilkan lewat antarmuka yang sederhana dan mudah dipahami anak.</p>
<h2>Cara kerja</h2>
<ol>
<li>Pengguna mengunggah gambar buah lewat halaman web.</li>
<li>Server Flask meneruskan gambar ke model klasifikasi TensorFlow.</li>
<li>Hasil prediksi jenis buah ditampilkan kembali di halaman.</li>
</ol>
<h2>Teknologi</h2>
<p>Model dibangun dengan <strong>TensorFlow</strong>, diintegrasikan ke aplikasi web dengan <strong>Flask</strong>, dan antarmukanya dibuat dengan <strong>Tailwind CSS</strong>.</p>
HTML,
                'highlights' => [
                    'Membangun model klasifikasi gambar buah menggunakan TensorFlow.',
                    'Mengintegrasikan model ke aplikasi web menggunakan Flask.',
                    'Merancang UI yang ramah anak-anak menggunakan Tailwind CSS.',
                ],
            ],
            [
                'slug' => 'web-administrasi-asrama',
                'title' => 'Web Administrasi Asrama',
                'summary' => 'Sistem administrasi asrama untuk mengelola data penghuni, pembayaran, dan kegiatan, lengkap dengan REST API.',
                'category' => 'web',
                'role' => 'Fullstack Developer',
                'year' => 2025,
                'stack' => ['React', 'Node.js', 'Tailwind CSS'],
                'repo_url' => 'https://github.com/deviansky/FE-WASE-02',
                'demo_url' => null,
                'thumbnail_path' => null,
                'is_featured' => false,
                'is_confidential' => false,
                'description' => <<<'HTML'
<h2>Gambaran proyek</h2>
<p>Aplikasi web untuk membantu pengelola asrama mengurus data penghuni, pembayaran, dan kegiatan dalam satu tempat. Proyek dikerjakan dalam tim kecil dengan pembagian tugas frontend dan backend.</p>
<h2>Fitur utama</h2>
<ul>
<li>Pengelolaan data penghuni asrama.</li>
<li>Pencatatan pembayaran.</li>
<li>Pengelolaan kegiatan asrama.</li>
<li>Autentikasi pengguna.</li>
</ul>
<h2>Arsitektur</h2>
<p>Frontend dibangun dengan <strong>React</strong> dan <strong>Tailwind CSS</strong>. Backend <strong>Node.js</strong> menyediakan REST API untuk autentikasi, manajemen data, dan integrasi dengan frontend.</p>
HTML,
                'highlights' => [
                    'Membangun antarmuka pengguna yang responsif.',
                    'Mengembangkan sistem administrasi untuk data penghuni, pembayaran, dan kegiatan.',
                    'Mengembangkan REST API untuk autentikasi, manajemen data, dan integrasi frontend.',
                ],
            ],
        ];
    }
}