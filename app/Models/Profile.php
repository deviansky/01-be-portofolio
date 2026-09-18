<?php

namespace App\Models;

use App\Models\Concerns\ResolvesMediaUrl;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use ResolvesMediaUrl;

    protected $fillable = [
        'name', 'short_name', 'headline', 'tagline', 'bio', 'focus_areas',
        'location', 'email', 'phone', 'avatar_path', 'cv_path',
        'github_url', 'linkedin_url', 'instagram_url', 'available_for_work',
    ];

    protected function casts(): array
    {
        return [
            'bio' => 'array',
            'focus_areas' => 'array',
            'available_for_work' => 'boolean',
        ];
    }

    public function avatarUrl(): ?string
    {
        return $this->mediaUrl($this->avatar_path);
    }

    public function cvUrl(): ?string
    {
        return $this->mediaUrl($this->cv_path);
    }
}
