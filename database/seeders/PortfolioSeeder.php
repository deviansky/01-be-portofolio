<?php

namespace Database\Seeders;

use App\Models\Certification;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Mengisi database dari database/seeders/data/portfolio.json.
 * File JSON itu sama bentuknya dengan fe-portofolio/src/data/fallback.js,
 * jadi cukup edit satu tempat lalu jalankan ulang:
 *
 *   php artisan db:seed --class=PortfolioSeeder
 *
 * Aman dijalankan berulang: data lama (kecuali pesan kontak) dihapus dulu.
 */
class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(
            file_get_contents(database_path('seeders/data/portfolio.json')),
            true,
            flags: JSON_THROW_ON_ERROR
        );

        DB::transaction(function () use ($data) {
            Project::query()->delete(); // project_images ikut terhapus (cascade)
            Skill::query()->delete();
            Experience::query()->delete();
            Education::query()->delete();
            Certification::query()->delete();
            Profile::query()->delete();

            $p = $data['profile'];
            Profile::create([
                ...Arr::only($p, [
                    'name',
                    'short_name',
                    'headline',
                    'tagline',
                    'bio',
                    'focus_areas',
                    'location',
                    'email',
                    'phone',
                    'available_for_work',
                ]),
                'avatar_path' => $p['avatar_url'] ?: null,
                'cv_path' => $p['cv_url'] ?: null,
                'github_url' => $p['socials']['github'] ?: null,
                'linkedin_url' => $p['socials']['linkedin'] ?: null,
                'instagram_url' => $p['socials']['instagram'] ?: null,
            ]);

            foreach ($data['skills'] as $i => $s) {
                Skill::create([...Arr::only($s, ['name', 'category']), 'sort_order' => $i]);
            }

            foreach ($data['projects'] as $i => $pr) {
                $project = Project::create([
                    ...Arr::only($pr, [
                        'slug',
                        'title',
                        'summary',
                        'description',
                        'highlights',
                        'category',
                        'role',
                        'stack',
                        'year',
                        'is_featured',
                        'is_confidential',
                    ]),
                    'thumbnail_path' => $pr['thumbnail_url'] ?: null,
                    'repo_url' => $pr['repo_url'] ?: null,
                    'demo_url' => $pr['demo_url'] ?: null,
                    'status' => 'published',
                    'sort_order' => $i,
                ]);

                foreach ($pr['images'] ?? [] as $j => $img) {
                    $project->images()->create([
                        'path' => $img['url'],
                        'caption' => $img['caption'] ?? null,
                        'sort_order' => $j,
                    ]);
                }
            }

            foreach ($data['experiences'] as $i => $e) {
                Experience::create([
                    ...Arr::only($e, [
                        'company',
                        'position',
                        'employment_type',
                        'location',
                        'work_mode',
                        'logo_url',
                        'started_at',
                        'ended_at',
                        'is_current',
                        'description',
                        'highlights',
                        'skills',
                    ]),
                    'sort_order' => $i,
                ]);
            }

            foreach ($data['educations'] as $i => $ed) {
                Education::create([
                    ...Arr::only($ed, ['institution', 'degree', 'field', 'location', 'work_mode', 'logo_url', 'started_at', 'ended_at', 'description', 'skills']),
                    'sort_order' => $i,
                ]);
            }

            foreach ($data['certifications'] as $i => $c) {
                Certification::create([
                    ...Arr::only($c, ['name', 'issuer', 'issued_at', 'credential_url']),
                    'sort_order' => $i,
                ]);
            }
        });
    }
}
