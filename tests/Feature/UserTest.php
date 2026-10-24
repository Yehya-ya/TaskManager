<?php

declare(strict_types=1);

use App\Models\Member;
use App\Models\Project;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\putJson;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

it('lists only the user and the users who share a project with them', function (): void {
    $ownedProject = Project::factory()->create(['user_id' => $this->user->id]);
    $memberOfOwnedProject = Member::factory()->withUser()->create(['project_id' => $ownedProject->id])->user;
    $ownerOfJoinedProject = Member::factory()->forEmail($this->user->email)->create()->project->owner;
    User::factory()->create();

    $response = getJson('/api/users');

    $response->assertOk()
        ->assertJsonStructure(['data' => ['*' => ['id', 'name', 'email', 'updated_at']]]);

    expect($response->json('data.*.id'))->toBe([$this->user->id, $memberOfOwnedProject->id, $ownerOfJoinedProject->id]);
});

it('shows a user who shares a project', function (): void {
    $project = Project::factory()->create(['user_id' => $this->user->id]);
    $other = Member::factory()->withUser()->create(['project_id' => $project->id])->user;

    $response = getJson("/api/users/{$other->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $other->id);
});

it('shows the user their own profile when they have no projects', function (): void {
    $response = getJson("/api/users/{$this->user->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $this->user->id);
});

it('returns 403 when showing a user who shares no project', function (): void {
    $stranger = User::factory()->create();

    getJson("/api/users/{$stranger->id}")->assertForbidden();
});

it('updates own user profile', function (): void {
    $response = putJson("/api/users/{$this->user->id}", [
        'name' => 'Updated Name',
        'email' => $this->user->email,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Updated Name');

    $this->user->refresh();
    expect($this->user->name)->toBe('Updated Name');
});

it('prevents updating other users', function (): void {
    $other = User::factory()->create();

    putJson("/api/users/{$other->id}", [
        'name' => 'Bad Update',
        'email' => $other->email,
    ])->assertForbidden();
});

it('deletes own user', function (): void {
    $response = deleteJson("/api/users/{$this->user->id}");

    $response->assertNoContent();
    $this->assertModelMissing($this->user);
});

it('prevents deleting other users', function (): void {
    $other = User::factory()->create();

    deleteJson("/api/users/{$other->id}")->assertForbidden();
});
