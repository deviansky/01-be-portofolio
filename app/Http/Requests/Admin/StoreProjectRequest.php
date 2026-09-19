<?php

namespace App\Http\Requests\Admin;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $titleVal = $this->input('title');
        $titleString = is_array($titleVal) ? ($titleVal['id'] ?? $titleVal['en'] ?? '') : $titleVal;

        if (empty($this->slug) && ! empty($titleString)) {
            $this->merge([
                'slug' => static::generateUniqueSlug($titleString),
            ]);
        }
    }

    public static function generateUniqueSlug(string $title, $ignoreId = null): string
    {
        $baseSlug = Str::slug($title) ?: 'proyek';
        $slug = $baseSlug;
        $count = 1;

        $query = Project::query()->where('slug', $slug);
        if ($ignoreId) {
            $id = is_object($ignoreId) ? $ignoreId->id : $ignoreId;
            $query->where('id', '!=', $id);
        }

        while ($query->exists()) {
            $count++;
            $slug = "{$baseSlug}-{$count}";
            $query = Project::query()->where('slug', $slug);
            if ($ignoreId) {
                $id = is_object($ignoreId) ? $ignoreId->id : $ignoreId;
                $query->where('id', '!=', $id);
            }
        }

        return $slug;
    }

    public function rules(): array
    {
        return [
            'title' => ['required'],
            'title.id' => ['required_if_is_array:title', 'nullable', 'string', 'max:255'],
            'title.en' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:projects,slug'],
            'summary' => ['required'],
            'summary.id' => ['required_if_is_array:summary', 'nullable', 'string'],
            'summary.en' => ['nullable', 'string'],
            'description' => ['nullable'],
            'category' => ['required', 'string', 'max:50'],
            'role' => ['nullable'],
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
