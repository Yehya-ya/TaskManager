<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

final class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
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
