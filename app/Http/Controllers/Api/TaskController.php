<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Http\Validator\MoveTaskValidator;
use App\Http\Validator\TaskValidator;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function index(Project $project): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', [Task::class, $project]);

        return TaskResource::collection($project->tasks);
    }

    public function show(Project $project, Task $task): TaskResource
    {
        Gate::authorize('view', [$task, $project]);

        return TaskResource::make($task->load('assignedUser', 'project', 'category'));
    }

    public function store(Request $request, Project $project): TaskResource
    {
        Gate::authorize('create', [Task::class, $project]);

        $attributes = (new TaskValidator)->validate(new Task, $project, $request->all());

        $task = $project->tasks()->create($attributes);

        return TaskResource::make($task);
    }

    public function update(Request $request, Project $project, Task $task): TaskResource
    {
        Gate::authorize('update', [$task, $project]);

        $attributes = (new TaskValidator)->validate($task, $project, $request->all());

        $task->category->touch();
        $task->update($attributes);

        return TaskResource::make($task);
    }

    public function destroy(Project $project, Task $task): JsonResponse
    {
        Gate::authorize('delete', [$task, $project]);

        $task->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    public function move(Request $request, Project $project, Task $task): TaskResource
    {
        Gate::authorize('update', [$task, $project]);

        $attributes = (new MoveTaskValidator)->validate($project, $request->all());

        $task->category->touch();
        $task->update($attributes);

        return new TaskResource($task);
    }
}
