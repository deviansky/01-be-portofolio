<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

/**
 * Isi proyek portofolio David.
 *
 *   php artisan db:seed --class=ContohProyekSeeder
 *
 * - Aman dijalankan berulang: memakai slug sebagai kunci (updateOrCreate),
 *   dan TIDAK menghapus proyek lain.
 * - Proyek lama yang isinya sudah digabung ke proyek baru (lihat
 *   SLUG_DIJADIKAN_DRAFT) dijadikan DRAFT, bukan dihapus. Bisa dipublikasikan
 *   lagi dari admin kalau masih dibutuhkan.
 * - Gambar sampul tidak diisi di sini; upload lewat admin. Catatan: menjalankan
 *   ulang seeder ini akan menimpa teks yang sudah kamu edit di admin, tapi
 *   tidak menghapus sampul.
 */
class ContohProyekSeeder extends Seeder
{
    /** Proyek lama yang isinya sudah tercakup proyek baru → dijadikan draft. */
    private const SLUG_DIJADIKAN_DRAFT = [
        'tap-sales-erp',     // → erp-distribusi
        'tap-sales-forces',  // → erp-distribusi (bagian tim sales lapangan)
        'journey-plan',      // → uni-tap
    ];

    public function run(): void
    {
        foreach ($this->projects() as $data) {
            Project::updateOrCreate(['slug' => $data['slug']], $data);
        }

        $jumlahDraft = Project::whereIn('slug', self::SLUG_DIJADIKAN_DRAFT)
            ->update(['status' => 'draft']);

        $this->command?->info(count($this->projects()).' proyek tersimpan; '.$jumlahDraft.' proyek lama dijadikan draft.');
    }

    private function projects(): array
    {
        return [
            // ───────────────────────── 1. Website company profile MIM
            [
                'slug' => 'mim-company-profile', // menimpa proyek lama dengan slug yang sama
                'title' => ' Company Profile PT Mitra Indo Maju',
                'summary' => 'Developed and maintained a WordPress-based company profile website, implementing requested changes and enhancing the homepage and other website pages.',
                'category' => 'web',
                'role' => 'Web Developer',
                'year' => 2025, // TODO: cek
                'stack' => ['WordPress', 'Elementor', 'HTML', 'CSS', 'JavaScript'],
                'repo_url' => null,
                'demo_url' => null, // TODO: URL website kalau boleh ditampilkan
                'is_featured' => true,
                'is_confidential' => false,
                'status' => 'published',
                'sort_order' => 1,
                'description' => <<<'HTML'
<h2>Project Overview</h2>
<p>The company profile website of PT Mitra Indo Maju, a beverage distribution company, was already running on WordPress before I joined the team. I was responsible for maintaining and developing the website, including implementing change requests from the team while ensuring that existing pages and components remained unaffected.</p>

<h2>What I Worked On</h2>
<ul>
<li>Implemented content and design changes based on the company's requirements.</li>
<li>Updated and enhanced the homepage, including the hero section, company information, brand carousel, news, and events.</li>
<li>Refined other pages to maintain visual consistency and a cohesive user experience.</li>
<li>Developed new sections and features, including a beverage product catalog organized by category and an interactive distributor map with 27 locations.</li>
</ul>

<h2>Technical Approach</h2>
<p>The website uses the free version of Elementor, so sections requiring custom layouts and interactions were implemented using custom HTML widgets. CSS was scoped individually for each widget to prevent conflicts between components and ensure that changes did not affect other sections or the WordPress theme's default styling.</p>
HTML,
                'highlights' => [],
            ],

            // ───────────────────────── 2. UNI TAP
            [
                'slug' => 'uni-tap',
                'title' => 'UNI TAP — Aplikasi Mobile Tim Sales',
                'summary' => 'Aplikasi mobile untuk salesman lapangan: kunjungan outlet, pengambilan barang di gudang, dan setoran penjualan, terhubung langsung ke sistem ERP.',
                'category' => 'mobile',
                'role' => 'Mobile Application Developer (magang)',
                'year' => 2025,
                'stack' => ['React Native', 'Laravel', 'MySQL'],
                'repo_url' => null,
                'demo_url' => null,
                'is_featured' => true,
                'is_confidential' => true,
                'status' => 'published',
                'sort_order' => 2,
                'description' => <<<'HTML'
<h2>Gambaran proyek</h2>
<p>UNI TAP adalah aplikasi mobile yang dipakai salesman setiap hari di lapangan. Aplikasi ini terhubung dengan backend ERP perusahaan, sehingga barang yang dibawa, kunjungan ke outlet, dan uang hasil penjualan tercatat di satu sistem.</p>
<h2>Yang saya kerjakan</h2>
<ul>
<li>Merombak antarmuka aplikasi secara end-to-end dan merancang ulang UI/UX-nya agar lebih mudah dipakai di lapangan.</li>
<li>Mengembangkan fitur Journey Plan untuk perencanaan kunjungan outlet, yang sekaligus menjadi topik tugas akhir saya.</li>
</ul>
<h2>Alur di lapangan</h2>
<ol>
<li>Salesman tap in di gudang dan menerima alokasi barang.</li>
<li>Saat tap out, salesman memotret kuitansi bertanda tangan sebagai bukti serah terima barang.</li>
<li>Salesman mengunjungi outlet dan mencatat penjualan.</li>
<li>Uang hasil penjualan, baik tunai maupun transfer dengan foto bukti, disetor untuk diverifikasi admin.</li>
</ol>
<p><em>Kode dan data aplikasi ini milik perusahaan, sehingga tidak dibagikan publik.</em></p>
HTML,
                'highlights' => [],
            ],

            // ───────────────────────── 3. ERP (sedang dikembangkan)
            [
                'slug' => 'erp-distribusi',
                'title' => 'Sistem ERP Distribusi',
                'summary' => 'Sistem ERP untuk perusahaan distribusi: penjualan, pembelian, gudang, promo, dan tim sales lapangan. Saat ini masih aktif dikembangkan.',
                'category' => 'erp',
                'role' => 'Fullstack Developer',
                'year' => 2026,
                'stack' => ['Laravel', 'React', 'Vite', 'MySQL'],
                'repo_url' => null,
                'demo_url' => null,
                'is_featured' => true,
                'is_confidential' => true,
                'status' => 'published',
                'sort_order' => 3,
                'description' => <<<'HTML'
<h2>Gambaran proyek</h2>
<p>ERP berbasis web untuk mengelola alur barang dan uang di perusahaan distribusi, mulai dari pembelian sampai penjualan dan penagihan. Satu backend Laravel melayani aplikasi web untuk kantor dan aplikasi mobile untuk tim sales.</p>
<h2>Modul yang saya kerjakan</h2>
<ul>
<li><strong>Penjualan:</strong> Sales Order dengan logika promo (diskon, FOC, cashback), Sales Invoice beserta cetaknya, dan Sales Return.</li>
<li><strong>Gudang:</strong> Receiving Order (gudang berikat, gudang utama, retur, transfer) dan Delivery Order (transfer dan konsinyasi).</li>
<li><strong>Pembelian:</strong> alur approval Purchase Request, Purchase Order, dan Consignment Order.</li>
<li><strong>Promo:</strong> manajemen diskon, FOC, dan cashback lengkap dengan alur persetujuan.</li>
<li><strong>Tim sales lapangan:</strong> alokasi barang harian ke salesman, kuitansi, dan setoran berjenjang dari salesman ke admin lalu ke team leader.</li>
</ul>
<h2>Keputusan teknis</h2>
<ul>
<li>Mutasi stok dicatat lewat helper terpusat, sehingga setiap perpindahan barang konsisten.</li>
<li>Notifikasi dikirim setelah transaksi database berhasil disimpan, sehingga notifikasi yang gagal tidak membatalkan transaksi.</li>
<li>Hak akses laporan diatur per peran (role-based access control).</li>
<li>Aliran uang di tim sales memakai kontrol silang: admin memverifikasi setoran, team leader mengecek admin.</li>
</ul>
<p><em>Proyek ini masih dalam pengembangan. Kode dan data milik perusahaan, sehingga tidak dibagikan publik.</em></p>
HTML,
                'highlights' => [],
            ],

            // ───────────────────────── 4. Website event
            [
                'slug' => 'go-to-thailand-lucky-draw', // menimpa proyek lama dengan slug yang sama
                'title' => 'Website Event Lucky Draw "Go to Thailand"',
                'summary' => 'Website pendaftaran event undian berhadiah perjalanan ke Thailand, dengan formulir peserta dan syarat mengikuti akun Instagram.',
                'category' => 'web',
                'role' => 'Frontend Developer',
                'year' => 2025, // TODO: cek
                'stack' => ['React'],
                'repo_url' => null,
                'demo_url' => null,
                'is_featured' => false,
                'is_confidential' => false,
                'status' => 'published',
                'sort_order' => 4,
                'description' => <<<'HTML'
<h2>Gambaran proyek</h2>
<p>Website publik untuk event undian berhadiah "Go to Thailand" dari PT Mitra Indo Maju. Pengunjung mendaftar sebagai peserta lewat formulir di website.</p>
<h2>Yang saya kerjakan</h2>
<ul>
<li>Membangun halaman publik dan formulir pendaftaran peserta.</li>
<li>Membuat syarat "ikuti Instagram" pada checkbox persetujuan: peserta harus mengikuti akun Instagram event sebelum bisa menyetujui syarat dan ketentuan.</li>
</ul>
HTML,
                'highlights' => [],
            ],

            // ───────────────────────── 5. EduVision (dari CV)
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
                'is_featured' => false,
                'is_confidential' => false,
                'status' => 'published',
                'sort_order' => 100,
                'description' => <<<'HTML'
<h2>Gambaran proyek</h2>
<p>EduVision adalah aplikasi web untuk anak-anak yang mengenali jenis buah dari gambar. Model machine learning dijalankan di server, lalu hasilnya ditampilkan lewat antarmuka yang sederhana dan mudah dipahami anak.</p>
<h2>Cara kerja</h2>
<ol>
<li>Pengguna mengunggah gambar buah lewat halaman web.</li>
<li>Server Flask meneruskan gambar ke model klasifikasi TensorFlow.</li>
<li>Hasil prediksi jenis buah ditampilkan kembali di halaman.</li>
</ol>
<h2>Yang saya kerjakan</h2>
<ul>
<li>Membangun model klasifikasi gambar buah menggunakan TensorFlow.</li>
<li>Mengintegrasikan model ke aplikasi web menggunakan Flask.</li>
<li>Merancang UI yang ramah anak-anak menggunakan Tailwind CSS.</li>
</ul>
HTML,
                'highlights' => [],
            ],

            // ───────────────────────── 6. Web Administrasi Asrama (dari CV)
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
                'is_featured' => false,
                'is_confidential' => false,
                'status' => 'published',
                'sort_order' => 101,
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
<h2>Yang saya kerjakan</h2>
<ul>
<li>Membangun antarmuka pengguna yang responsif dengan React dan Tailwind CSS.</li>
<li>Mengembangkan REST API Node.js untuk autentikasi, manajemen data, dan integrasi dengan frontend.</li>
</ul>
HTML,
                'highlights' => [],
            ],
        ];
    }
}