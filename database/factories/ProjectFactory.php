<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'summary' => $this->faker->paragraph(),
            'description' => '<p>' . $this->faker->paragraph() . '</p>',
            'category' => $this->faker->randomElement(['Web', 'ERP', 'Mobile']),
            'role' => 'Fullstack Developer',
            'year' => 2026,
            'stack' => ['React', 'Laravel'],
            'highlights' => ['Feature 1', 'Feature 2'],
            'thumbnail_path' => null,
            'repo_url' => null,
            'demo_url' => null,
            'is_featured' => false,
            'is_confidential' => false,
            'status' => 'published',
            'sort_order' => 0,
        ];
    }
}
