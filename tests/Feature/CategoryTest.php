<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);

    $this->project = Project::factory()->create(['user_id' => $this->user->id]);
});

it('lists project categories', function (): void {
    $categories = Category::factory(3)->create(['project_id' => $this->project->id]);

    $response = getJson("/api/projects/{$this->project->id}/categories");

    $response->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'updated_at'],
            ],
        ]);
});

it('shows category details', function (): void {
    $category = Category::factory()
        ->has(Task::factory()->count(3))
        ->create(['project_id' => $this->project->id]);

    $response = getJson("/api/projects/{$this->project->id}/categories/{$category->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'updated_at',
                'project',
                'tasks',
            ],
        ]);
});

it('creates new category', function (): void {
    $response = postJson("/api/projects/{$this->project->id}/categories", [
        'title' => 'New Category',
    ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'data' => ['id', 'title'],
        ]);

    $this->assertDatabaseHas('categories', [
        'title' => 'New Category',
        'project_id' => $this->project->id,
    ]);
});

it('updates category details', function (): void {
    $category = Category::factory()->create(['project_id' => $this->project->id]);

    $response = putJson("/api/projects/{$this->project->id}/categories/{$category->id}", [
        'title' => 'Updated Category',
    ]);

    $response->assertOk();

    $category->refresh();
    expect($category->title)->toBe('Updated Category');
});

it('deletes category', function (): void {
    $category = Category::factory()->create(['project_id' => $this->project->id]);

    $response = deleteJson("/api/projects/{$this->project->id}/categories/{$category->id}");

    $response->assertNoContent();
    $this->assertModelMissing($category);
});

it('prevents unauthorized category access', function (): void {
    $otherProject = Project::factory()->create();
    $category = Category::factory()->create(['project_id' => $otherProject->id]);

    // Try to access category from another project
    getJson("/api/projects/{$otherProject->id}/categories/{$category->id}")
        ->assertForbidden();

    // Try to create category in another project
    postJson("/api/projects/{$otherProject->id}/categories", [
        'title' => 'New Category',
    ])->assertForbidden();

    // Try to update category in another project
    putJson("/api/projects/{$otherProject->id}/categories/{$category->id}", [
        'title' => 'Updated Category',
    ])->assertForbidden();

    // Try to delete category from another project
    deleteJson("/api/projects/{$otherProject->id}/categories/{$category->id}")
        ->assertForbidden();
});

it('validates required fields for category', function (): void {
    // Test store validation
    postJson("/api/projects/{$this->project->id}/categories", [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title']);

    // Test update validation
    $category = Category::factory()->create(['project_id' => $this->project->id]);
    putJson("/api/projects/{$this->project->id}/categories/{$category->id}", [
        'title' => '',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title']);
});
