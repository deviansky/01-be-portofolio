<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Project */
class AdminProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $thumbnailUrl = null;
        if ($this->thumbnail_path) {
            $thumbnailUrl = str_starts_with($this->thumbnail_path, 'http')
                ? $this->thumbnail_path
                : asset('storage/' . ltrim($this->thumbnail_path, '/'));
        }

        $enTitle = $this->getTranslation('title', 'en', false);

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->getTranslations('title'),
            'summary' => $this->getTranslations('summary'),
            'description' => $this->getTranslations('description'),
            'role' => $this->getTranslations('role'),
            'highlights' => $this->highlights ?? [],
            'category' => $this->category,
            'stack' => $this->stack ?? [],
            'year' => (int) $this->year,
            'thumbnail_path' => $this->thumbnail_path,
            'thumbnail_url' => $thumbnailUrl,
            'repo_url' => $this->repo_url,
            'demo_url' => $this->demo_url,
            'is_featured' => (bool) $this->is_featured,
            'is_confidential' => (bool) $this->is_confidential,
            'status' => $this->status,
            'sort_order' => (int) $this->sort_order,
            'has_english' => ! empty($enTitle),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'images' => $this->whenLoaded('images', function () {
                return $this->images->map(function ($img) {
                    $url = null;
                    if ($img->path) {
                        $url = str_starts_with($img->path, 'http')
                            ? $img->path
                            : asset('storage/' . ltrim($img->path, '/'));
                    }
                    return [
                        'id' => $img->id,
                        'path' => $img->path,
                        'url' => $url,
                        'caption' => $img->caption,
                        'sort_order' => $img->sort_order,
                    ];
                });
            }, []),
        ];
    }
}
