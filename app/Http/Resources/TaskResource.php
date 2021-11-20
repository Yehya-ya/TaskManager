<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class TaskResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'assigned_user' => UserResource::make($this->whenLoaded('assignedUser')),
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'project' => ProjectResource::make($this->whenLoaded('project')),

            $this->merge(Arr::except(parent::toArray($request), [
                'assigned_user',
                'category_id',
                'project_id',
                'created_at',
                'updated_at'
            ]))
        ];
    }
}
