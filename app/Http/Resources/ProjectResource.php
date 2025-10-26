<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

final class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'owner' => UserResource::make($this->whenLoaded('owner')),
            'members' => MemberResource::collection($this->whenLoaded('members')),
            'tasks' => TaskResource::collection($this->whenLoaded('tasks')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),

            $this->merge(Arr::except(parent::toArray($request), [
                'created_at',
                'pivot',
            ])),
        ];
    }
}
