<?php

namespace App\Http\Validator;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Validation\Rule;

class TaskValidator
{
    public function validate(Task $task, Project $project, array $attributes): array
    {
        return validator($attributes,
            [
                'title' => [Rule::when($task->exists, 'sometimes'), 'required', 'string', 'max:255'],
                'category_id' => [Rule::when($task->exists, 'sometimes'), 'required', 'integer', Rule::exists('categories', 'id')->where('project_id', $project->id)],

                'description' => ['string'],
                'end_at' => ['date', 'date:Y-m-d', 'after:today'],
                'assigned_user' => ['string', 'email', 'max:255', Rule::exists('project_user', 'email')->where('project_id', $project->id)],
            ]
        )->validate();
    }
}