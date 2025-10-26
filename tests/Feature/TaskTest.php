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

    $this->project = Project::factory()
        ->has(Category::factory()->count(3))
        ->create(['user_id' => $this->user->id]);
});

it('lists project tasks', function (): void {
    $tasks = Task::factory(3)->create([
        'project_id' => $this->project->id,
        'category_id' => $this->project->categories->first()->id,
    ]);

    $response = getJson("/api/projects/{$this->project->id}/tasks");

    $response->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'description', 'end_at', 'updated_at'],
            ],
        ]);
});

it('shows task details', function (): void {
    $task = Task::factory()->create([
        'project_id' => $this->project->id,
        'category_id' => $this->project->categories->first()->id,
    ]);

    $response = getJson("/api/projects/{$this->project->id}/tasks/{$task->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'description',
                'end_at',
                'updated_at',
                'assigned_user',
                'project',
                'category',
            ],
        ]);
});

it('creates new task', function (): void {
    $category = $this->project->categories->first();

    $response = postJson("/api/projects/{$this->project->id}/tasks", [
        'title' => 'New Task',
        'description' => 'Task Description',
        'category_id' => $category->id,
        'end_at' => now()->addDays(7)->toDateTimeString(),
    ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'data' => ['id', 'title', 'description', 'end_at'],
        ]);

    $this->assertDatabaseHas('tasks', [
        'title' => 'New Task',
        'project_id' => $this->project->id,
        'category_id' => $category->id,
    ]);
});

it('updates task details', function (): void {
    $task = Task::factory()->create([
        'project_id' => $this->project->id,
        'category_id' => $this->project->categories->first()->id,
    ]);

    $response = putJson("/api/projects/{$this->project->id}/tasks/{$task->id}", [
        'title' => 'Updated Task',
        'description' => 'Updated Description',
        'category_id' => $task->category_id,
        'end_at' => now()->addDays(10)->toDateTimeString(),
    ]);

    $response->assertOk();

    $task->refresh();
    expect($task->title)->toBe('Updated Task')
        ->and($task->description)->toBe('Updated Description');
});

it('deletes task', function (): void {
    $task = Task::factory()->create([
        'project_id' => $this->project->id,
        'category_id' => $this->project->categories->first()->id,
    ]);

    $response = deleteJson("/api/projects/{$this->project->id}/tasks/{$task->id}");

    $response->assertNoContent();
    $this->assertModelMissing($task);
});

it('moves task to different category', function (): void {
    $task = Task::factory()->create([
        'project_id' => $this->project->id,
        'category_id' => $this->project->categories->first()->id,
    ]);

    $newCategory = $this->project->categories->last();

    $response = putJson("/api/projects/{$this->project->id}/tasks/{$task->id}/move", [
        'category_id' => $newCategory->id,
    ]);

    $response->assertOk();

    $task->refresh();
    expect($task->category_id)->toBe($newCategory->id);
});

it('prevents unauthorized task access', function (): void {
    $otherProject = Project::factory()
        ->has(Category::factory())
        ->create();

    $task = Task::factory()->create([
        'project_id' => $otherProject->id,
        'category_id' => $otherProject->categories->first()->id,
    ]);

    // Try to access task from another project
    getJson("/api/projects/{$otherProject->id}/tasks/{$task->id}")
        ->assertForbidden();

    // Try to create task in another project
    postJson("/api/projects/{$otherProject->id}/tasks", [
        'title' => 'New Task',
        'category_id' => $otherProject->categories->first()->id,
    ])->assertForbidden();

    // Try to update task in another project
    putJson("/api/projects/{$otherProject->id}/tasks/{$task->id}", [
        'title' => 'Updated Task',
    ])->assertForbidden();

    // Try to delete task from another project
    deleteJson("/api/projects/{$otherProject->id}/tasks/{$task->id}")
        ->assertForbidden();
});
