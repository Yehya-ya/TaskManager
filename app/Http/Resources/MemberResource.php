<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class MemberResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'project' => ProjectResource::make($this->whenLoaded('project')),
            $this->merge(Arr::except(parent::toArray($request), [
                'project_id',
                'created_at',
            ])),
        ];
    }
}
