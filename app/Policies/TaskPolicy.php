<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TaskPolicy
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
        return (($project->user_id === $user->id || $project->members()->where('user_id', $user->id)->exists()) && $project->tasks()->where('id', $task->id)->exists())
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
        return (($project->user_id === $user->id || optional($task->assigned_user)->id === $user->id) && $project->tasks()->where('id', $task->id)->exists())
            ? Response::allow()
            : Response::deny();
    }

    public function delete(User $user, Task $task, Project $project): Response
    {
        return ($project->user_id === $user->id && $project->tasks()->where('id', $task->id)->exists())
            ? Response::allow()
            : Response::deny();
    }
}
