<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

final class ProjectPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Project $project): Response
    {
        return ($project->user_id === $user->id || $project->members()->where('user_id', $user->id)->exists())
            ? Response::allow()
            : Response::deny();
    }

    public function update(User $user, Project $project): Response
    {
        return $project->user_id === $user->id
            ? Response::allow()
            : Response::deny();
    }

    public function delete(User $user, Project $project): Response
    {
        return $project->user_id === $user->id
            ? Response::allow()
            : Response::deny();
    }
}
