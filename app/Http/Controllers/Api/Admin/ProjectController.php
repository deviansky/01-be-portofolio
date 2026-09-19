<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectRequest;
use App\Http\Requests\Admin\UpdateProjectRequest;
use App\Http\Resources\AdminProjectResource;
use App\Models\Project;
use App\Services\ProjectHtmlSanitizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Project::query()->with('images');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%")
                    ->orWhere('stack', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            if ($category !== 'all' && $category !== 'Semua') {
                $query->where('category', $category);
            }
        }

        if ($status = $request->input('status')) {
            if ($status !== 'all' && $status !== 'Semua') {
                $query->where('status', strtolower($status));
            }
        }

        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $perPage = (int) $request->input('per_page', 15);
        $projects = $query->paginate($perPage);

        return AdminProjectResource::collection($projects);
    }

    public function show(int $id): AdminProjectResource
    {
        $project = Project::with('images')->findOrFail($id);
        return new AdminProjectResource($project);
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if (isset($validated['description'])) {
            $validated['description'] = ProjectHtmlSanitizer::clean($validated['description']);
        }

        $project = DB::transaction(function () use ($validated) {
            $images = $validated['images'] ?? [];
            unset($validated['images']);

            $project = Project::create($validated);

            foreach ($images as $i => $img) {
                $project->images()->create([
                    'path' => $img['path'],
                    'caption' => $img['caption'] ?? null,
                    'sort_order' => $i,
                ]);
            }

            return $project;
        });

        return (new AdminProjectResource($project->load('images')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateProjectRequest $request, int $id): AdminProjectResource
    {
        $project = Project::with('images')->findOrFail($id);
        $validated = $request->validated();

        if (array_key_exists('description', $validated)) {
            $validated['description'] = ProjectHtmlSanitizer::clean($validated['description']);
        }

        DB::transaction(function () use ($project, $validated) {
            $images = $validated['images'] ?? null;
            unset($validated['images']);

            $project->update($validated);

            if ($images !== null) {
                $project->images()->delete();
                foreach ($images as $i => $img) {
                    $project->images()->create([
                        'path' => $img['path'],
                        'caption' => $img['caption'] ?? null,
                        'sort_order' => $i,
                    ]);
                }
            }
        });

        return new AdminProjectResource($project->load('images'));
    }

    public function destroy(int $id): Response
    {
        $project = Project::with('images')->findOrFail($id);

        if ($project->thumbnail_path && Storage::disk('public')->exists($project->thumbnail_path)) {
            Storage::disk('public')->delete($project->thumbnail_path);
        }

        foreach ($project->images as $img) {
            if ($img->path && Storage::disk('public')->exists($img->path)) {
                Storage::disk('public')->delete($img->path);
            }
        }

        $project->delete();

        return response()->noContent();
    }
}
