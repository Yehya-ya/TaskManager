<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final class VerifyAccessToken
{
    public function __invoke(): JsonResponse
    {
        return response()->json(['data' => UserResource::make(auth()->user())], Response::HTTP_OK);
    }
}
