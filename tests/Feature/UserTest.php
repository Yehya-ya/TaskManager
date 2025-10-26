<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\putJson;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

it('lists all users', function (): void {
    User::factory()->count(3)->create();

    $response = getJson('/api/users');

    $response->assertOk()
        ->assertJsonStructure(['data' => ['*' => ['id', 'name', 'email', 'updated_at']]]);
});

it('shows specific user', function (): void {
    $other = User::factory()->create();

    $response = getJson("/api/users/{$other->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $other->id);
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
