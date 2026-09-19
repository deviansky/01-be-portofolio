<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $routeParam = $this->route('id') ?? $this->route('project');
        $projectId = is_object($routeParam) ? $routeParam->id : $routeParam;

        $titleVal = $this->input('title');
        $titleString = is_array($titleVal) ? ($titleVal['id'] ?? $titleVal['en'] ?? '') : $titleVal;

        if (empty($this->slug) && ! empty($titleString)) {
            $this->merge([
                'slug' => StoreProjectRequest::generateUniqueSlug($titleString, $projectId),
            ]);
        }
    }

    public function rules(): array
    {
        $routeParam = $this->route('id') ?? $this->route('project');
        $projectId = is_object($routeParam) ? $routeParam->id : $routeParam;

        return [
            'title' => ['required'],
            'title.id' => ['nullable', 'string', 'max:255'],
            'title.en' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('projects', 'slug')->ignore($projectId)],
            'summary' => ['required'],
            'summary.id' => ['nullable', 'string'],
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
