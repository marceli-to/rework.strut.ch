<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopicResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'uuid' => $this->uuid,
			'title' => $this->title,
			'slug' => $this->slug,
			'publish' => $this->publish,
			'sort_order' => $this->sort_order,
		];
	}
}
