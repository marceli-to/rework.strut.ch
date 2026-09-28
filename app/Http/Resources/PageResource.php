<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'uuid' => $this->uuid,
			'key' => $this->key,
			'is_content' => $this->isContent(),
			'title' => $this->title,
			'text' => $this->text,
			'meta_description' => $this->meta_description,
			'publish' => $this->publish,
			'media' => MediaResource::collection($this->whenLoaded('media')),
		];
	}
}
