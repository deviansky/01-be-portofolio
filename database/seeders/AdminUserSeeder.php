<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Membuat / memperbarui akun admin panel portofolio.
 *
 *   php artisan db:seed --class=AdminUserSeeder
 *
 * Password di-hash saat seeder dijalankan, jadi di database tersimpan
 * sebagai hash ($2y$...), bukan teks asli.
 *
 * PENTING: file ini berisi password asli. Sudah didaftarkan di .gitignore
 * agar tidak ikut ter-push ke GitHub (repo publik).
 *
 * Menjalankan ulang = memperbarui password akun yang sama, bukan membuat dobel.
 * Tidak dipanggil oleh DatabaseSeeder / PortfolioSeeder.
 */
class AdminUserSeeder extends Seeder
{
    private const USERNAME = 'david';

    private const PASSWORD = 'password';

    public function run(): void
    {
        $exists = DB::table('users')->where('username', self::USERNAME)->exists();

        $data = [
            'name' => self::USERNAME,
            'password' => Hash::make(self::PASSWORD),
            'updated_at' => now(),
        ];

        if ($exists) {
            DB::table('users')->where('username', self::USERNAME)->update($data);
            $this->command?->info('Password akun admin "'.self::USERNAME.'" diperbarui.');
        } else {
            DB::table('users')->insert([...$data, 'username' => self::USERNAME, 'created_at' => now()]);
            $this->command?->info('Akun admin "'.self::USERNAME.'" dibuat.');
        }
    }
}