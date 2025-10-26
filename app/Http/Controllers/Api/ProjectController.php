<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Validator\AddProjectMemberValidator;
use App\Http\Validator\RemoveProjectMemberValidator;
use App\Models\Member;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

final class ProjectController
{
    public function index(): AnonymousResourceCollection
    {
        return ProjectResource::collection(auth()->user()->projects()->orderBy('id')->get());
    }

    public function show(Project $project): ProjectResource
    {
        Gate::authorize('view', $project);

        return ProjectResource::make($project->load(['owner', 'members', 'tasks', 'categories']));
    }

    public function store(StoreProjectRequest $request): ProjectResource
    {
        /** @var Project */
        $project = auth()->user()->projects()->create($request->validated());

        $project->categories()->createMany([
            ['title' => 'ToDo'],
            ['title' => 'Doing'],
            ['title' => 'Done'],
        ]);

        return ProjectResource::make($project);
    }

    public function update(UpdateProjectRequest $request, Project $project): ProjectResource
    {
        Gate::authorize('update', $project);

        $project->update($request->validated());

        return ProjectResource::make($project);
    }

    public function destroy(Project $project): JsonResponse
    {
        Gate::authorize('delete', $project);

        $project->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    public function addMember(Request $request, Project $project): ProjectResource
    {
        Gate::authorize('update', $project);

        $email = (new AddProjectMemberValidator)->validate($project, auth()->user(), $request->all())['email'];

        Member::query()->create([
            'user_id' => User::query()->firstWhere('email', $email)?->id,
            'project_id' => $project->id,
            'email' => $email,
        ]);

        return ProjectResource::make($project->load('members'));
    }

    public function removeMember(Request $request, Project $project): ProjectResource
    {
        Gate::authorize('update', $project);

        $member_id = (new RemoveProjectMemberValidator)->validate($project, auth()->user(), $request->all())['member_id'];

        Member::query()->findOrFail($member_id)->delete();

        return ProjectResource::make($project->load('members'));
    }
}
