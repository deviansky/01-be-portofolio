<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Profile */
class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'short_name' => $this->short_name,
            'headline' => $this->headline,
            'tagline' => $this->tagline,
            'bio' => $this->bio ?? [],
            'focus_areas' => $this->focus_areas ?? [],
            'location' => $this->location,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar_url' => $this->avatarUrl(),
            'cv_url' => $this->cvUrl(),
            'socials' => [
                'github' => $this->github_url,
                'linkedin' => $this->linkedin_url,
                'instagram' => $this->instagram_url,
            ],
            'available_for_work' => $this->available_for_work,
        ];
    }
}
