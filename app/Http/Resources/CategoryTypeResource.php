<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryTypeResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'uuid' => $this->uuid,
			'category_id' => $this->whenLoaded('category', fn () => $this->category->uuid),
			'name_singular' => $this->name_singular,
			'name_plural' => $this->name_plural,
			'publish' => $this->publish,
			'sort_order' => $this->sort_order,
			'category' => new CategoryResource($this->whenLoaded('category')),
		];
	}
}
