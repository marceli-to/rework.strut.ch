<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'uuid' => $this->uuid,
			'title' => $this->title,
			'location' => $this->location,
			'slug' => $this->slug,
			'subtitle' => $this->subtitle,
			'year' => $this->year,
			'description' => $this->description,
			'info' => $this->info,
			'meta_description' => $this->meta_description,
			'publish' => $this->publish,
			'feature' => $this->feature,
			'detail' => $this->detail,
			'sort_order' => $this->sort_order,
			'topic' => new TopicResource($this->whenLoaded('topic')),
			'media' => MediaResource::collection($this->whenLoaded('media')),
		];
	}
}
