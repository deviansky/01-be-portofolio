<?php

namespace App\Models;

use App\Models\Concerns\ResolvesMediaUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectImage extends Model
{
    use ResolvesMediaUrl;

    protected $fillable = ['project_id', 'path', 'caption', 'sort_order'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function url(): ?string
    {
        return $this->mediaUrl($this->path);
    }
}
