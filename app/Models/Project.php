<?php

namespace App\Models;

use App\Models\Concerns\ResolvesMediaUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory, ResolvesMediaUrl;

    protected $fillable = [
        'slug',
        'title',
        'summary',
        'description',
        'highlights',
        'category',
        'role',
        'stack',
        'thumbnail_path',
        'repo_url',
        'demo_url',
        'year',
        'is_featured',
        'is_confidential',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'highlights' => 'array',
            'stack' => 'array',
            'year' => 'integer',
            'is_featured' => 'boolean',
            'is_confidential' => 'boolean',
        ];
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }

    /** Hanya proyek yang boleh tampil di publik, urutan: unggulan dulu. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('year');
    }

    public function thumbnailUrl(): ?string
    {
        return $this->mediaUrl($this->thumbnail_path);
    }
}
