<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GridItemResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'uuid' => $this->uuid,
			'position' => $this->position,
			'type' => $this->type(),
			'media' => $this->media ? new GridMediaResource($this->media) : null,
			'news' => $this->news ? [
				'uuid' => $this->news->uuid,
				'title' => $this->news->title,
				'date_label' => $this->news->date_label,
				'publish' => $this->news->publish,
			] : null,
		];
	}
}
