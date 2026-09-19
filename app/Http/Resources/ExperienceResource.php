<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Experience */
class ExperienceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company' => $this->company,
            'position' => $this->position,
            'employment_type' => $this->employment_type,
            'location' => $this->location,
            'work_mode' => $this->work_mode,
            'logo_url' => $this->logo_url,
            'started_at' => $this->started_at?->toDateString(),
            'ended_at' => $this->ended_at?->toDateString(),
            'is_current' => $this->is_current,
            'description' => $this->description,
            'highlights' => $this->highlights ?? [],
            'skills' => $this->skills ?? [],
        ];
    }
}
