<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\postJson;

it('logs out and deletes the current access token', function (): void {
    $user = User::factory()->create();

    $token = $user->createToken('test')->plainTextToken;

    $response = postJson('/api/logout', [], ['Authorization' => "Bearer {$token}"]);

    $response->assertNoContent();

    $this->assertDatabaseMissing('personal_access_tokens', [
        'tokenable_id' => $user->id,
    ]);
});

it('requires authentication to logout', function (): void {
    postJson('/api/logout', [])->assertUnauthorized();
});
