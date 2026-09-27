<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GridRowResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'uuid' => $this->uuid,
			'area' => $this->area,
			'layout' => $this->layout,
			'publish' => $this->publish,
			'sort_order' => $this->sort_order,
			'items' => GridItemResource::collection($this->whenLoaded('items')),
		];
	}
}
