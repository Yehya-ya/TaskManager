<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class VerifyAccessToken extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json(["massage" => "success"], Response::HTTP_OK);
    }
}