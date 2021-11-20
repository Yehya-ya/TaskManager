<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'projects' => ProjectResource::collection($this->whenLoaded('projects')),
            'projects_member_in' => ProjectResource::collection($this->whenLoaded('projectsMemberIn')),

            $this->merge(Arr::except(parent::toArray($request), [
                'id',
                'pivot',
                'email_verified_at',
                'password',
                'created_at',
                'updated_at',
            ])),
        ];
    }
}
