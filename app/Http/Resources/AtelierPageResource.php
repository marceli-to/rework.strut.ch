<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AtelierPageResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'uuid' => $this->uuid,
			'slug' => $this->slug,
			'title' => $this->title,
			'text' => $this->text,
			'publish' => $this->publish,
			'media' => $this->whenLoaded('media', fn () => MediaResource::collection($this->media)),
		];
	}
}
