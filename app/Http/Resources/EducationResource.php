<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Education */
class EducationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'institution' => $this->institution,
            'degree' => $this->degree,
            'field' => $this->field,
            'location' => $this->location,
            'work_mode' => $this->work_mode,
            'logo_url' => $this->logo_url,
            'started_at' => $this->started_at?->toDateString(),
            'ended_at' => $this->ended_at?->toDateString(),
            'description' => $this->description,
            'skills' => $this->skills ?? [],
        ];
    }
}
