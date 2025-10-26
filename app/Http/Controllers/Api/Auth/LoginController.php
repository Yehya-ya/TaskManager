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
        $attributes = $login_request->validated();

        throw_unless(Auth::attempt($attributes, false), ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]));

        return response()->json([
            'token' => User::query()->firstWhere('email', $attributes['email'])->createToken('test')->plainTextToken,
        ], Response::HTTP_OK);
    }
}
