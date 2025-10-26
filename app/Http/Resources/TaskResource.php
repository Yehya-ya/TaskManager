<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

final class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'assigned_user' => UserResource::make($this->whenLoaded('assignedUser')),
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'project' => ProjectResource::make($this->whenLoaded('project')),

            $this->merge(Arr::except(parent::toArray($request), [
                'created_at',
            ])),
        ];
    }
}
