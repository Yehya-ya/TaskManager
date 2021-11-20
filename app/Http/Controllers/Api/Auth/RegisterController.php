<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegisterRequest;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $attribute = $request->validated();

        $user = User::create([
            'name' => $attribute['name'],
            'email' => $attribute['email'],
            'password' => Hash::make($attribute['password']),
        ]);

        Member::where('email', $user->email)->update(['user_id' => $user->id]);

        $token = $user->createToken("" . now());

        return response()->json([
            'token' => $token->plainTextToken,
        ]);
    }


}
