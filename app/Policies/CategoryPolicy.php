<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user, Project $project): Response
    {
        return ($project->user_id === $user->id || $project->members()->where('user_id', $user->id)->exists())
            ? Response::allow()
            : Response::deny();
    }

    public function view(User $user, Category $category, Project $project): Response
    {
        return (($project->user_id === $user->id || $project->members()->where('user_id', $user->id)->exists()) && $project->categories()->where('id', $category->id)->exists())
            ? Response::allow()
            : Response::deny();
    }

    public function create(User $user, Project $project): Response
    {
        return $project->user_id === $user->id
            ? Response::allow()
            : Response::deny();
    }

    public function update(User $user, Category $category, Project $project): Response
    {
        return ($project->user_id === $user->id && $project->categories()->where('id', $category->id)->exists())
            ? Response::allow()
            : Response::deny();
    }

    public function delete(User $user, Category $category, Project $project): Response
    {
        return ($project->user_id === $user->id && $project->categories()->where('id', $category->id)->exists())
            ? Response::allow()
            : Response::deny();
    }
}
