<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $login_request): JsonResponse
    {
        $attributes = $login_request->validated();

        $user = User::where('email', $attributes['email'])->first();
        if (!Hash::check($attributes['password'], $user->password)) {
            return response()->json('incorrect password.', 403);
        }

        return response()->json([
            'token' => $user->createToken('test')->plainTextToken,
        ], Response::HTTP_OK);
    }
}
