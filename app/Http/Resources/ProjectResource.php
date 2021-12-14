<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class ProjectResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'owner' => UserResource::make($this->whenLoaded('owner')),
            'members' => MemberResource::collection($this->whenLoaded('members')),
            'tasks' => TaskResource::collection($this->whenLoaded('tasks')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),

            $this->merge(Arr::except(parent::toArray($request), [
                'created_at',
                'updated_at',
                'pivot'
            ])),
        ];
    }
}
