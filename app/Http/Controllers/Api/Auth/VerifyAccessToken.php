<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class VerifyAccessToken extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json(['data' => UserResource::make(auth()->user())], Response::HTTP_OK);
    }
}
