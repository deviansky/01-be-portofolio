<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Ringkasan proyek untuk list. Field detail (description, highlights, images)
 * hanya ikut kalau relasi images sudah di-load, yaitu di endpoint detail.
 *
 * @mixin \App\Models\Project
 */
class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isDetail = $this->relationLoaded('images');

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'summary' => $this->summary,
            'category' => $this->category,
            'role' => $this->role,
            'stack' => $this->stack ?? [],
            'thumbnail_url' => $this->thumbnailUrl(),
            'repo_url' => $this->repo_url,
            'demo_url' => $this->demo_url,
            'year' => $this->year,
            'is_featured' => $this->is_featured,
            'is_confidential' => $this->is_confidential,
            $this->mergeWhen($isDetail, fn () => [
                'description' => $this->description,
                'highlights' => $this->highlights ?? [],
                'images' => $this->images->map(fn ($img) => [
                    'url' => $img->url(),
                    'caption' => $img->caption,
                ])->values(),
            ]),
        ];
    }
}
