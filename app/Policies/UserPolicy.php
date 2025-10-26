<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

final class UserPolicy
{
    use HandlesAuthorization;

    public function update(User $user, User $model): Response
    {
        return $user->id === $model->id
            ? Response::allow()
            : Response::deny();
    }

    public function delete(User $user, User $model): Response
    {
        return $user->id === $model->id
            ? Response::allow()
            : Response::deny();
    }
}
