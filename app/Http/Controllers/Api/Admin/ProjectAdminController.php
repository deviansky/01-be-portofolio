<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectAdminController extends Controller
{
    /** GET /api/admin/projects — Daftar semua proyek (draft & published). */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'category' => ['nullable', 'in:erp,web,mobile'],
            'status' => ['nullable', 'in:draft,published'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $query = Project::query()
            ->when($request->category, fn($q, $cat) => $q->where('category', $cat))
            ->when($request->status, fn($q, $st) => $q->where('status', $st))
            ->when($request->q, fn($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('year')
            ->orderByDesc('id');

        $projects = $query->get()->map(fn(Project $p) => $this->formatProject($p));

        return response()->json([
            'data' => $projects,
        ]);
    }

    /** GET /api/admin/projects/{id} — Detail proyek untuk form edit. */
    public function show(int $id): JsonResponse
    {
        $project = Project::with('images')->findOrFail($id);

        return response()->json([
            'data' => $this->formatProject($project, true),
        ]);
    }

    /** POST /api/admin/projects — Membuat proyek baru. */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:projects,slug'],
            'summary' => ['required', 'string', 'max:300'],
            'description' => ['nullable', 'string'],
            'highlights' => ['nullable', 'array'],
            'highlights.*' => ['string'],
            'category' => ['required', 'in:erp,web,mobile'],
            'role' => ['nullable', 'string', 'max:255'],
            'stack' => ['nullable', 'array'],
            'stack.*' => ['string'],
            'thumbnail_path' => ['nullable', 'string', 'max:255'],
            'repo_url' => ['nullable', 'url', 'max:255'],
            'demo_url' => ['nullable', 'url', 'max:255'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2099'],
            'is_featured' => ['nullable', 'boolean'],
            'is_confidential' => ['nullable', 'boolean'],
            'status' => ['required', 'in:draft,published'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']);
            // Unikkan slug jika sudah digunakan
            $originalSlug = $validated['slug'];
            $count = 1;
            while (Project::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = "{$originalSlug}-{$count}";
                $count++;
            }
        }

        $project = Project::create($validated);

        return response()->json([
            'message' => 'Proyek berhasil dibuat.',
            'data' => $this->formatProject($project, true),
        ], 201);
    }

    /** PUT /api/admin/projects/{id} — Memperbarui data proyek. */
    public function update(Request $request, int $id): JsonResponse
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', "unique:projects,slug,{$id}"],
            'summary' => ['required', 'string', 'max:300'],
            'description' => ['nullable', 'string'],
            'highlights' => ['nullable', 'array'],
            'highlights.*' => ['string'],
            'category' => ['required', 'in:erp,web,mobile'],
            'role' => ['nullable', 'string', 'max:255'],
            'stack' => ['nullable', 'array'],
            'stack.*' => ['string'],
            'thumbnail_path' => ['nullable', 'string', 'max:255'],
            'repo_url' => ['nullable', 'url', 'max:255'],
            'demo_url' => ['nullable', 'url', 'max:255'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2099'],
            'is_featured' => ['nullable', 'boolean'],
            'is_confidential' => ['nullable', 'boolean'],
            'status' => ['required', 'in:draft,published'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $project->update($validated);

        return response()->json([
            'message' => 'Proyek berhasil diperbarui.',
            'data' => $this->formatProject($project->fresh('images'), true),
        ]);
    }

    /** DELETE /api/admin/projects/{id} — Menghapus proyek. */
    public function destroy(int $id): JsonResponse
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json([
            'message' => 'Proyek berhasil dihapus.',
        ]);
    }

    /** POST /api/admin/projects/{id}/thumbnail — Mengunggah thumbnail proyek. */
    public function uploadThumbnail(Request $request, int $id): JsonResponse
    {
        $project = Project::findOrFail($id);

        $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
        ]);

        $path = $request->file('image')->store('projects', 'public');
        $project->update(['thumbnail_path' => $path]);

        return response()->json([
            'message' => 'Thumbnail berhasil diunggah.',
            'thumbnail_url' => $project->thumbnailUrl(),
            'data' => $this->formatProject($project, true),
        ]);
    }

    private function formatProject(Project $p, bool $includeDetails = false): array
    {
        $data = [
            'id' => $p->id,
            'slug' => $p->slug,
            'title' => $p->title,
            'summary' => $p->summary,
            'category' => $p->category,
            'role' => $p->role,
            'stack' => $p->stack ?? [],
            'thumbnail_path' => $p->thumbnail_path,
            'thumbnail_url' => $p->thumbnailUrl(),
            'repo_url' => $p->repo_url,
            'demo_url' => $p->demo_url,
            'year' => $p->year,
            'is_featured' => (bool) $p->is_featured,
            'is_confidential' => (bool) $p->is_confidential,
            'status' => $p->status,
            'sort_order' => $p->sort_order,
            'created_at' => $p->created_at?->toIso8601String(),
            'updated_at' => $p->updated_at?->toIso8601String(),
        ];

        if ($includeDetails) {
            $data['description'] = $p->description;
            $data['highlights'] = $p->highlights ?? [];
            $data['images'] = $p->relationLoaded('images')
                ? $p->images->map(fn($img) => ['id' => $img->id, 'url' => $img->url(), 'caption' => $img->caption])->values()
                : [];
        }

        return $data;
    }
}
