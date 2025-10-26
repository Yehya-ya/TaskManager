<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

final class UserController
{
    public function index(): AnonymousResourceCollection
    {
        return UserResource::collection(User::all());
    }

    public function show(User $user): JsonResource
    {
        return UserResource::make($user);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResource
    {
        Gate::authorize('update', $user);

        $user->update($request->validated());

        return UserResource::make($user);
    }

    public function destroy(User $user): JsonResponse
    {
        Gate::authorize('delete', $user);

        $user->delete();

        return response()->json('', Response::HTTP_NO_CONTENT);
    }
}
