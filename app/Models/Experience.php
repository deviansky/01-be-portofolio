<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Experience extends Model
{
    use HasTranslations;

    public array $translatable = [
        'position',
        'employment_type',
        'location',
        'work_mode',
        'description',
    ];

    protected $fillable = [
        'company',
        'position',
        'employment_type',
        'location',
        'work_mode',
        'logo_url',
        'started_at',
        'ended_at',
        'is_current',
        'description',
        'highlights',
        'skills',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'ended_at' => 'date',
            'is_current' => 'boolean',
            'highlights' => 'array',
            'skills' => 'array',
        ];
    }
}
