<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntryResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'uuid' => $this->uuid,
			'type' => $this->type->value,
			'project_id' => $this->whenLoaded('project', fn () => $this->project?->uuid),
			'project' => $this->whenLoaded('project', fn () => $this->project?->full_title),
			'title' => $this->title,
			'description' => $this->description,
			'year' => $this->year,
			'url' => $this->url,
			'publish' => $this->publish,
			'media' => MediaResource::collection($this->whenLoaded('media')),
		];
	}
}
