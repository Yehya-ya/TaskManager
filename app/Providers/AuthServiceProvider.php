<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Policies\CategoryPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\TaskPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Task::class => TaskPolicy::class,
        Project::class => ProjectPolicy::class,
        Category::class => CategoryPolicy::class,
        User::class => UserPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
