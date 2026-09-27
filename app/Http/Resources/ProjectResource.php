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
			'category_type_id' => $this->whenLoaded('categoryType', fn () => $this->categoryType->uuid),
			'title' => $this->title,
			'name' => $this->name,
			'location' => $this->location,
			'full_title' => $this->full_title,
			'slug' => $this->slug,
			'year' => $this->year,
			'description' => $this->description,
			'info' => $this->info,
			'status' => $this->status?->value,
			'status_label' => $this->status?->label(),
			'competition' => $this->competition?->value,
			'has_detail' => $this->has_detail,
			'meta_description' => $this->getRawOriginal('meta_description'),
			'publish' => $this->publish,
			'sort_order' => $this->sort_order,
			'category_type' => new CategoryTypeResource($this->whenLoaded('categoryType')),
			'media' => MediaResource::collection($this->whenLoaded('media')),
		];
	}
}
