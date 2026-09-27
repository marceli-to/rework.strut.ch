<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'uuid' => $this->uuid,
			'date_label' => $this->date_label,
			'title' => $this->title,
			'subtitle' => $this->subtitle,
			'text' => $this->text,
			'link_url' => $this->link_url,
			'link_label' => $this->link_label,
			'publish' => $this->publish,
			'media' => MediaResource::collection($this->whenLoaded('media')),
		];
	}
}
