<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Database\Seeders\PortfolioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminProjectTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'username' => 'admin_test',
        ]);
    }

    public function test_unauthenticated_user_cannot_access_admin_projects(): void
    {
        $response = $this->getJson('/api/admin/projects');
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_project(): void
    {
        Sanctum::actingAs($this->user);

        $payload = [
            'title' => 'Project Alpha',
            'slug' => 'project-alpha',
            'summary' => 'Summary of Alpha',
            'description' => '<p>Description of Alpha</p>',
            'category' => 'Web',
            'role' => 'Fullstack Developer',
            'year' => 2026,
            'stack' => ['React', 'Laravel'],
            'status' => 'published',
            'is_featured' => true,
            'is_confidential' => false,
        ];

        $response = $this->postJson('/api/admin/projects', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Project Alpha')
            ->assertJsonPath('data.slug', 'project-alpha');

        $this->assertDatabaseHas('projects', [
            'slug' => 'project-alpha',
            'title' => 'Project Alpha',
        ]);
    }

    public function test_authenticated_user_can_read_project(): void
    {
        Sanctum::actingAs($this->user);

        $project = Project::factory()->create([
            'title' => 'Read Me Project',
            'slug' => 'read-me-project',
        ]);

        $response = $this->getJson("/api/admin/projects/{$project->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Read Me Project');
    }

    public function test_authenticated_user_can_update_project(): void
    {
        Sanctum::actingAs($this->user);

        $project = Project::factory()->create([
            'title' => 'Old Title',
            'slug' => 'old-title',
        ]);

        $payload = [
            'title' => 'New Updated Title',
            'slug' => 'old-title',
            'summary' => 'Updated summary text',
            'category' => 'ERP',
            'role' => 'Backend Engineer',
            'year' => 2026,
            'status' => 'published',
        ];

        $response = $this->putJson("/api/admin/projects/{$project->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'New Updated Title');

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'New Updated Title',
        ]);
    }

    public function test_authenticated_user_can_delete_project_and_cleanup_storage(): void
    {
        Sanctum::actingAs($this->user);
        Storage::fake('public');

        $thumbPath = 'projects/test_thumb.png';
        $galleryPath = 'projects/test_gallery.png';
        Storage::disk('public')->put($thumbPath, 'fake_content');
        Storage::disk('public')->put($galleryPath, 'fake_content');

        $project = Project::factory()->create([
            'thumbnail_path' => $thumbPath,
        ]);
        $project->images()->create([
            'path' => $galleryPath,
            'sort_order' => 0,
        ]);

        $response = $this->deleteJson("/api/admin/projects/{$project->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);

        $this->assertFalse(Storage::disk('public')->exists($thumbPath));
        $this->assertFalse(Storage::disk('public')->exists($galleryPath));
    }

    public function test_duplicate_slug_returns_422(): void
    {
        Sanctum::actingAs($this->user);

        Project::factory()->create(['slug' => 'existing-slug']);

        $payload = [
            'title' => 'Another Project',
            'slug' => 'existing-slug',
            'summary' => 'Summary text',
            'category' => 'Web',
            'role' => 'Developer',
            'year' => 2026,
            'status' => 'published',
        ];

        $response = $this->postJson('/api/admin/projects', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['slug']);
    }

    public function test_html_sanitizer_removes_script_tags_and_onclick_and_foreign_images(): void
    {
        Sanctum::actingAs($this->user);

        $dirtyHtml = '<p>Hello <script>alert("xss")</script><a href="https://example.com" onclick="alert(1)" style="color:red">Link</a><img src="https://foreign-site.com/evil.png"><img src="http://localhost:8000/storage/projects/local.png"></p>';

        $payload = [
            'title' => 'Sanitize Test',
            'slug' => 'sanitize-test',
            'summary' => 'Summary text',
            'description' => $dirtyHtml,
            'category' => 'Web',
            'role' => 'Developer',
            'year' => 2026,
            'status' => 'published',
        ];

        $response = $this->postJson('/api/admin/projects', $payload);
        $response->assertStatus(201);

        $savedDesc = Project::where('slug', 'sanitize-test')->value('description');

        $this->assertStringNotContainsString('<script>', $savedDesc);
        $this->assertStringNotContainsString('onclick', $savedDesc);
        $this->assertStringNotContainsString('style=', $savedDesc);
        $this->assertStringNotContainsString('foreign-site.com', $savedDesc);
        $this->assertStringContainsString('target="_blank"', $savedDesc);
        $this->assertStringContainsString('rel="noopener noreferrer"', $savedDesc);
        $this->assertStringContainsString('http://localhost:8000/storage/projects/local.png', $savedDesc);
    }

    public function test_image_upload(): void
    {
        Sanctum::actingAs($this->user);
        Storage::fake('public');

        $file = UploadedFile::fake()->image('test_image.png', 100, 100);

        $response = $this->postJson('/api/admin/uploads', [
            'image' => $file,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['path', 'url']);

        $path = $response->json('path');
        $this->assertTrue(Storage::disk('public')->exists($path));
    }

    public function test_images_synchronization(): void
    {
        Sanctum::actingAs($this->user);

        $project = Project::factory()->create();
        $project->images()->createMany([
            ['path' => 'projects/old1.png', 'sort_order' => 0],
            ['path' => 'projects/old2.png', 'sort_order' => 1],
        ]);

        $payload = [
            'title' => $project->title,
            'slug' => $project->slug,
            'summary' => $project->summary,
            'category' => $project->category,
            'role' => $project->role,
            'year' => $project->year,
            'status' => 'published',
            'images' => [
                ['path' => 'projects/old2.png', 'caption' => 'Updated Caption'],
                ['path' => 'projects/new3.png', 'caption' => 'New Image'],
            ],
        ];

        $response = $this->putJson("/api/admin/projects/{$project->id}", $payload);

        $response->assertStatus(200);

        $images = $project->fresh()->images()->orderBy('sort_order')->get();
        $this->assertCount(2, $images);
        $this->assertEquals('projects/old2.png', $images[0]->path);
        $this->assertEquals(0, $images[0]->sort_order);
        $this->assertEquals('projects/new3.png', $images[1]->path);
        $this->assertEquals(1, $images[1]->sort_order);
    }

    public function test_filter_category_and_status(): void
    {
        Sanctum::actingAs($this->user);

        Project::factory()->create(['title' => 'ERP Draft', 'category' => 'ERP', 'status' => 'draft']);
        Project::factory()->create(['title' => 'Web Published', 'category' => 'Web', 'status' => 'published']);

        $response = $this->getJson('/api/admin/projects?category=ERP&status=draft');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'ERP Draft');
    }

    public function test_draft_projects_do_not_appear_in_public_api(): void
    {
        \App\Models\Profile::create([
            'name' => 'John Doe',
            'short_name' => 'John',
            'headline' => 'Fullstack Developer',
            'email' => 'john@example.com',
        ]);

        Project::factory()->create([
            'title' => 'Draft Secret Project',
            'slug' => 'draft-secret-project',
            'status' => 'draft',
        ]);

        $portfolioResponse = $this->getJson('/api/portfolio');
        $portfolioResponse->assertStatus(200);

        $projects = $portfolioResponse->json('data.projects');
        $titles = collect($projects)->pluck('title');
        $this->assertNotContains('Draft Secret Project', $titles);

        $projectResponse = $this->getJson('/api/projects/draft-secret-project');
        $projectResponse->assertStatus(404);
    }

    public function test_portfolio_seeder_preserves_existing_projects(): void
    {
        $customProject = Project::factory()->create([
            'title' => 'Custom Persisted Project',
            'slug' => 'custom-persisted-project',
        ]);

        $seeder = new PortfolioSeeder();
        $seeder->run();

        $this->assertDatabaseHas('projects', [
            'id' => $customProject->id,
            'slug' => 'custom-persisted-project',
        ]);
    }
}
