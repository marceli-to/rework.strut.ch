<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeoSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'landing_meta_description' => $this->landing_meta_description,
            'projects_meta_description' => $this->projects_meta_description,
            'werkliste_meta_description' => $this->werkliste_meta_description,
            'profile_meta_description' => $this->profile_meta_description,
            'team_meta_description' => $this->team_meta_description,
            'jobs_meta_description' => $this->jobs_meta_description,
            'contact_meta_description' => $this->contact_meta_description,
            'media' => MediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
