<?php

declare(strict_types=1);

namespace App\Http\Validator;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;

final class TaskValidator
{
    public function validate(Task $task, Project $project, array $attributes): array
    {
        return validator($attributes,
            [
                'title' => [Rule::when($task->exists, 'sometimes'), 'required', 'string', 'max:255'],
                'category_id' => [Rule::when($task->exists, 'sometimes'), 'required', 'integer', Rule::exists('categories', 'id')->where('project_id', $project->id)],

                'description' => ['string', 'nullable'],
                'end_at' => ['date', 'date:Y-m-d', 'after:today', 'nullable'],
                'assigned_user' => [
                    'integer',
                    Rule::exists('users', 'id')->where(fn (Builder $query): Builder => $query
                        ->where('id', $project->user_id)
                        ->orWhereIn('id', $project->members()->select('user_id'))),
                    'nullable',
                ],
            ]
        )->validate();
    }
}
