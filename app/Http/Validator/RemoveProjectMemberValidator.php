<?php

declare(strict_types=1);

namespace App\Http\Validator;

use App\Models\Project;
use App\Models\User;
use Illuminate\Validation\Rule;

final class RemoveProjectMemberValidator
{
    public function validate(Project $project, User $user, array $attributes): array
    {
        return validator($attributes,
            [
                'member_id' => [
                    'required',
                    'integer',
                    Rule::exists('members', 'id')->where('project_id', $project->id),
                ],
            ]
        )->validate();
    }
}
