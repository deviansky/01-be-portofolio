<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:projects,slug'],
            'summary' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'string', 'max:50'],
            'role' => ['required', 'string', 'max:100'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'stack' => ['nullable', 'array'],
            'highlights' => ['nullable', 'array'],
            'thumbnail_path' => ['nullable', 'string'],
            'repo_url' => ['nullable', 'string'],
            'demo_url' => ['nullable', 'string'],
            'is_featured' => ['boolean'],
            'is_confidential' => ['boolean'],
            'status' => ['required', 'in:published,draft'],
            'images' => ['nullable', 'array'],
            'images.*.path' => ['required_with:images', 'string'],
            'images.*.caption' => ['nullable', 'string'],
        ];
    }
}
