<?php

declare(strict_types=1);

use App\Models\Member;
use App\Models\User;

use function Pest\Laravel\postJson;

it('can register a new user', function (): void {
    $response = postJson('/api/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'passworD1*',
        'password_confirmation' => 'passworD1*',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['token']);

    expect($response->json('token'))->not->toBeEmpty();

    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
});

it('assigns member records to newly registered user', function (): void {
    // Create a member record with unassigned user_id
    $member = Member::factory()->create([
        'email' => 'john@example.com',
        'user_id' => null,
    ]);

    $response = postJson('/api/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'passworD1*',
        'password_confirmation' => 'passworD1*',
    ]);

    $response->assertOk();

    $user = User::query()->where('email', 'john@example.com')->first();

    expect($member->fresh()->user_id)->toBe($user->id);
});

it('validates required fields for registration', function (): void {
    $response = postJson('/api/register', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

it('prevents registration with existing email', function (): void {
    User::factory()->create(['email' => 'john@example.com']);

    $response = postJson('/api/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});
