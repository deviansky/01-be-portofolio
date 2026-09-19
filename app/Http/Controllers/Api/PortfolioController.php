<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CertificationResource;
use App\Http\Resources\EducationResource;
use App\Http\Resources\ExperienceResource;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\SkillResource;
use App\Models\Certification;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;

class PortfolioController extends Controller
{
    /**
     * GET /api/portfolio
     * Semua data halaman utama dalam satu request supaya FE cukup fetch sekali.
     */
    public function index(): JsonResponse
    {
        $profile = Profile::query()->first();

        if (! $profile) {
            return response()->json([
                'message' => 'Profil belum diisi. Jalankan: php artisan db:seed --class=PortfolioSeeder',
            ], 503);
        }

        return response()->json([
            'data' => [
                'profile' => new ProfileResource($profile),
                'skills' => SkillResource::collection(
                    Skill::query()->orderBy('category')->orderBy('sort_order')->get()
                ),
                'projects' => ProjectResource::collection(Project::query()->published()->get()),
                'experiences' => ExperienceResource::collection(
                    Experience::query()->orderByDesc('is_current')->orderBy('sort_order')->orderByDesc('started_at')->get()
                ),
                'educations' => EducationResource::collection(
                    Education::query()->orderBy('sort_order')->orderByDesc('started_at')->get()
                ),
                'certifications' => CertificationResource::collection(
                    Certification::query()->orderBy('sort_order')->orderByDesc('issued_at')->get()
                ),
            ],
        ]);
    }
}
