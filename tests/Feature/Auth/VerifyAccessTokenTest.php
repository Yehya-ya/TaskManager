<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\postJson;

it('returns the authenticated user data when token is valid', function (): void {
    $user = User::factory()->create();

    $token = $user->createToken('test')->plainTextToken;

    $response = postJson('/api/verify_access_token', [], ['Authorization' => "Bearer {$token}"]);

    $response->assertOk()
        ->assertJsonPath('data.id', $user->id);
});

it('requires authentication to verify token', function (): void {
    postJson('/api/verify_access_token')->assertUnauthorized();
});
