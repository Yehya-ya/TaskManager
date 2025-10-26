<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\User\RegisterRequest;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

final class RegisterController
{
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $attribute = $request->validated();

        $user = User::query()->create([
            'name' => $attribute['name'],
            'email' => $attribute['email'],
            'password' => Hash::make($attribute['password']),
        ]);

        Member::query()->where('email', $user->email)->update(['user_id' => $user->id]);

        $token = $user->createToken(''.now());

        return response()->json([
            'token' => $token->plainTextToken,
        ]);
    }
}
