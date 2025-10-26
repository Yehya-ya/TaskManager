<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

final class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'projects' => ProjectResource::collection($this->whenLoaded('projects')),
            'projects_member_in' => ProjectResource::collection($this->whenLoaded('projectsMemberIn')),

            $this->merge(Arr::except(parent::toArray($request), [
                'pivot',
                'email_verified_at',
                'password',
                'created_at',
            ])),
        ];
    }
}
