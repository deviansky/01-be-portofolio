<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Education extends Model
{
    use HasTranslations;

    public array $translatable = [
        'degree',
        'field',
        'description',
    ];

    protected $table = 'educations';

    protected $fillable = [
        'institution',
        'degree',
        'field',
        'location',
        'work_mode',
        'logo_url',
        'started_at',
        'ended_at',
        'description',
        'skills',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'ended_at' => 'date',
            'skills' => 'array',
        ];
    }
}
