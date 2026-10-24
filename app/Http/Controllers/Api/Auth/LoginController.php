<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\User\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

final class LoginController
{
    public function __invoke(LoginRequest $login_request): JsonResponse
    {
        $credentials = $login_request->safe()->only(['email', 'password']);

        throw_unless(Auth::attempt($credentials, false), ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]));

        $tokenName = $login_request->validated('device_name') ?? 'api';

        return response()->json([
            'token' => User::query()->firstWhere('email', $credentials['email'])->createToken($tokenName)->plainTextToken,
        ], Response::HTTP_OK);
    }
}
