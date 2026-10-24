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

it('names the token after the device', function (array $device, string $tokenName): void {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    $response = postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password',
        ...$device,
    ]);

    $response->assertOk();

    $this->assertDatabaseHas('personal_access_tokens', [
        'tokenable_id' => $user->id,
        'name' => $tokenName,
    ]);
})->with([
    'the given device name' => [['device_name' => 'Pixel 8'], 'Pixel 8'],
    'the default when it is missing' => [[], 'api'],
    'the default when it is null' => [['device_name' => null], 'api'],
]);

it('rejects a device_name longer than 255 characters', function (): void {
    $response = postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password',
        'device_name' => str_repeat('a', 256),
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['device_name' => 'The device name field must not be greater than 255 characters.']);
});
