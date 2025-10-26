<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Member;
use App\Models\Project;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

it('lists user projects', function (): void {
    $projects = Project::factory(3)->create(['user_id' => $this->user->id]);
    $otherUserProject = Project::factory()->create();

    $response = getJson('/api/projects');

    $response->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'description', 'updated_at'],
            ],
        ]);

    // Should not include other user's project
    expect($response->json('data.*.id'))->not->toContain($otherUserProject->id);
});

it('shows project details', function (): void {
    $project = Project::factory()
        ->has(Category::factory()->count(3))
        ->create(['user_id' => $this->user->id]);

    $response = getJson("/api/projects/{$project->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'description',
                'updated_at',
                'owner',
                'members',
                'tasks',
                'categories',
            ],
        ]);
});

it('prevents viewing projects user does not own or is not member of', function (): void {
    $project = Project::factory()->create();

    $response = getJson("/api/projects/{$project->id}");

    $response->assertForbidden();
});

it('creates new project with default categories', function (): void {
    $response = postJson('/api/projects', [
        'title' => 'New Project',
        'description' => 'Project Description',
    ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'data' => ['id', 'title', 'description'],
        ]);

    $project = Project::query()->find($response->json('data.id'));

    // Verify default categories were created
    $categories = $project->categories()->pluck('title')->toArray();
    expect($categories)->toContain('ToDo', 'Doing', 'Done');
});

it('updates project details', function (): void {
    $project = Project::factory()->create(['user_id' => $this->user->id]);

    $response = putJson("/api/projects/{$project->id}", [
        'title' => 'Updated Title',
        'description' => 'Updated Description',
    ]);

    $response->assertOk();

    $project->refresh();
    expect($project->title)->toBe('Updated Title')
        ->and($project->description)->toBe('Updated Description');
});

it('deletes project', function (): void {
    $project = Project::factory()->create(['user_id' => $this->user->id]);

    $response = deleteJson("/api/projects/{$project->id}");

    $response->assertNoContent();
    $this->assertModelMissing($project);
});

it('adds member to project', function (): void {
    $project = Project::factory()->create(['user_id' => $this->user->id]);
    $newMemberEmail = 'newmember@example.com';

    $response = putJson("/api/projects/{$project->id}/add-member", [
        'email' => $newMemberEmail,
    ]);

    $response->assertOk();

    $this->assertDatabaseHas('members', [
        'project_id' => $project->id,
        'email' => $newMemberEmail,
    ]);
});

it('removes member from project', function (): void {
    $project = Project::factory()->create(['user_id' => $this->user->id]);
    $member = Member::factory()->create([
        'project_id' => $project->id,
        'email' => 'member@example.com',
    ]);

    $response = putJson("/api/projects/{$project->id}/remove-member", [
        'member_id' => $member->id,
    ]);

    $response->assertOk();
    $this->assertModelMissing($member);
});
