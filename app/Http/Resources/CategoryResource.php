<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'uuid' => $this->uuid,
			'name' => $this->name,
			'show_types' => $this->show_types,
			'publish' => $this->publish,
			'sort_order' => $this->sort_order,
			'types' => CategoryTypeResource::collection($this->whenLoaded('types')),
		];
	}
}
