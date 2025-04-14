<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $login_request): JsonResponse
    {
        $attributes = $login_request->validated();

        if (! Auth::attempt($attributes, false)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        return response()->json([
            'token' => User::firstWhere('email', $attributes['email'])->createToken('test')->plainTextToken,
        ], Response::HTTP_OK);
    }
}
