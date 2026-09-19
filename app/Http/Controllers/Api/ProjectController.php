<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    /** GET /api/projects?category=erp|web|mobile */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate(['category' => ['nullable', 'in:erp,web,mobile']]);

        $projects = Project::query()
            ->published()
            ->when($request->category, fn ($q, $cat) => $q->where('category', $cat))
            ->get();

        return ProjectResource::collection($projects);
    }

    /** GET /api/projects/{slug} — detail lengkap termasuk gambar. */
    public function show(string $slug): ProjectResource
    {
        $project = Project::query()
            ->where('status', 'published')
            ->where('slug', $slug)
            ->with('images')
            ->firstOrFail();

        return new ProjectResource($project);
    }
}
