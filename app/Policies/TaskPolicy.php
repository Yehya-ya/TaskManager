<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

final class TaskPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user, Project $project): Response
    {
        return ($project->user_id === $user->id || $project->members()->where('user_id', $user->id)->exists())
            ? Response::allow()
            : Response::deny();
    }

    public function view(User $user, Task $task, Project $project): Response
    {
        return (($project->user_id === $user->id || $project->members()->where('user_id', $user->id)->exists()) && $task->project_id === $project->id)
            ? Response::allow()
            : Response::deny();
    }

    public function create(User $user, Project $project): Response
    {
        return $project->user_id === $user->id
            ? Response::allow()
            : Response::deny();
    }

    public function update(User $user, Task $task, Project $project): Response
    {
        return (($project->user_id === $user->id || $task->assigned_user === $user->id) && $task->project_id === $project->id)
            ? Response::allow()
            : Response::deny();
    }

    public function delete(User $user, Task $task, Project $project): Response
    {
        return ($project->user_id === $user->id && $task->project_id === $project->id)
            ? Response::allow()
            : Response::deny();
    }
}
