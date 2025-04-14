<?php

namespace App\Http\Validator;

use App\Models\Project;
use Illuminate\Validation\Rule;

class MoveTaskValidator
{
    public function validate(Project $project, $attributes): array
    {
        return validator($attributes,
            [
                'category_id' => [
                    'required',
                    'int',
                    Rule::exists('categories', 'id')->where('project_id', $project->id),
                ],
            ]
        )->validate();
    }
}
