<?php

declare(strict_types=1);

namespace App\Http\Validator;

use App\Models\Project;
use App\Models\User;
use Illuminate\Validation\Rule;

final class AddProjectMemberValidator
{
    public function validate(Project $project, User $user, array $attributes): array
    {
        return validator($attributes,
            [
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('members', 'email')->where('project_id', $project->id),
                ],
            ]
        )->validate();
    }
}
