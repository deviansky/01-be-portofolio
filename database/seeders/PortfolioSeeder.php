<?php

namespace Database\Seeders;

use App\Models\Certification;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Mengisi database profil, keahlian, pengalaman, pendidikan, dan sertifikasi
 * dari database/seeders/data/profile-content.json (dwibahasa).
 *
 * Jalankan:
 *   php artisan db:seed --class=PortfolioSeeder
 *
 * Aman dijalankan berulang. Tabel projects TIDAK disentuh.
 */
class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $profileContentPath = database_path('seeders/data/profile-content.json');
        if (!file_exists($profileContentPath)) {
            $profileContentPath = database_path('seeders/data/portfolio.json');
        }

        $data = json_decode(
            file_get_contents($profileContentPath),
            true,
            flags: JSON_THROW_ON_ERROR
        );

        DB::transaction(function () use ($data) {
            // Hapus data lama profil, skill, experience, education, certification
            // JANGAN menyentuh tabel projects!
            Skill::query()->delete();
            Experience::query()->delete();
            Education::query()->delete();
            Certification::query()->delete();
            Profile::query()->delete();

            $p = $data['profile'];
            Profile::create([
                'name' => $p['name'] ?? '',
                'short_name' => $p['short_name'] ?? '',
                'headline' => $p['headline'] ?? '',
                'tagline' => $p['tagline'] ?? '',
                'bio' => $p['bio'] ?? [],
                'focus_areas' => $p['focus_areas'] ?? [],
                'location' => $p['location'] ?? '',
                'languages' => $p['languages'] ?? [],
                'email' => $p['email'] ?? '',
                'phone' => $p['phone'] ?? '',
                'avatar_path' => $p['avatar_url'] ?: null,
                'cv_path' => $p['cv_url'] ?: null,
                'github_url' => $p['socials']['github'] ?? null,
                'linkedin_url' => $p['socials']['linkedin'] ?? null,
                'instagram_url' => $p['socials']['instagram'] ?? null,
                'available_for_work' => $p['available_for_work'] ?? true,
            ]);

            foreach ($data['skills'] as $i => $s) {
                Skill::create([
                    'name' => $s['name'],
                    'category' => $s['category'],
                    'sort_order' => $i,
                ]);
            }

            foreach ($data['experiences'] as $i => $e) {
                Experience::create([
                    'company' => $e['company'] ?? '',
                    'position' => $e['position'] ?? '',
                    'employment_type' => $e['employment_type'] ?? null,
                    'location' => $e['location'] ?? null,
                    'work_mode' => $e['work_mode'] ?? null,
                    'logo_url' => $e['logo_url'] ?? null,
                    'started_at' => $e['started_at'] ?? null,
                    'ended_at' => $e['ended_at'] ?? null,
                    'is_current' => $e['is_current'] ?? false,
                    'description' => $e['description'] ?? null,
                    'highlights' => $e['highlights'] ?? [],
                    'skills' => $e['skills'] ?? [],
                    'sort_order' => $i,
                ]);
            }

            foreach ($data['educations'] as $i => $ed) {
                Education::create([
                    'institution' => $ed['institution'] ?? '',
                    'degree' => $ed['degree'] ?? null,
                    'field' => $ed['field'] ?? null,
                    'location' => $ed['location'] ?? null,
                    'work_mode' => $ed['work_mode'] ?? null,
                    'logo_url' => $ed['logo_url'] ?? null,
                    'started_at' => $ed['started_at'] ?? null,
                    'ended_at' => $ed['ended_at'] ?? null,
                    'description' => $ed['description'] ?? null,
                    'skills' => $ed['skills'] ?? [],
                    'sort_order' => $i,
                ]);
            }

            foreach ($data['certifications'] as $i => $c) {
                Certification::create([
                    'name' => $c['name'] ?? '',
                    'issuer' => $c['issuer'] ?? '',
                    'issued_at' => $c['issued_at'] ?? null,
                    'credential_url' => $c['credential_url'] ?? null,
                    'sort_order' => $i,
                ]);
            }
        });
    }
}
