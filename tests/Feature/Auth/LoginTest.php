<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\postJson;

it('can login with valid credentials', function (): void {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    $response = postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['token']);

    expect($response->json('token'))->not->toBeEmpty();
});

it('cannot login with invalid credentials', function (): void {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    $response = postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('validates required fields for login', function (): void {
    $response = postJson('/api/login', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'password']);
});

it('returns 429 after five login attempts for the same email', function (): void {
    $credentials = ['email' => 'test@example.com', 'password' => 'wrong-password'];

    for ($attempt = 1; $attempt <= 5; $attempt++) {
        postJson('/api/login', $credentials)->assertUnprocessable();
    }

    postJson('/api/login', $credentials)->assertTooManyRequests();
});

it('does not throttle logins for other emails', function (): void {
    for ($attempt = 1; $attempt <= 5; $attempt++) {
        postJson('/api/login', ['email' => 'test@example.com', 'password' => 'wrong-password']);
    }

    $response = postJson('/api/login', ['email' => 'other@example.com', 'password' => 'wrong-password']);

    $response->assertUnprocessable();
});
