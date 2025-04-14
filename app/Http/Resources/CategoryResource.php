<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'project' => ProjectResource::make($this->whenLoaded('project')),
            'tasks' => TaskResource::collection($this->whenLoaded('tasks')),

            $this->merge(Arr::except(parent::toArray($request), [
                'created_at',
            ])),
        ];
    }
}
