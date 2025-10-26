<?php

declare(strict_types=1);

namespace App\Http\Validator;

use App\Models\Project;
use Illuminate\Validation\Rule;

final class MoveTaskValidator
{
    public function validate(Project $project, array $attributes): array
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
