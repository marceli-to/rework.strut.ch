<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		$cropParam = $this->crop && isset($this->crop['w'], $this->crop['h'], $this->crop['x'], $this->crop['y'])
			? '&crop=' . $this->crop['w'] . ',' . $this->crop['h'] . ',' . $this->crop['x'] . ',' . $this->crop['y']
			: '';

		return [
			'id' => $this->id,
			'uuid' => $this->uuid,
			'file' => $this->file,
			'original_name' => $this->original_name,
			'mime_type' => $this->mime_type,
			'size' => $this->size,
			'alt' => $this->alt,
			'caption' => $this->caption,
			'width' => $this->width,
			'height' => $this->height,
			'crop' => $this->crop,
			'variant' => $this->variant,
			'orientation' => $this->orientation,
			'is_teaser' => $this->is_teaser,
			'is_og' => $this->is_og,
			'sort_order' => $this->sort_order,
			'original_url' => '/storage/uploads/' . $this->file,
			'thumbnail_url' => '/img/uploads/' . $this->file . '?w=400&h=400&fit=crop' . $cropParam,
			'preview_url' => '/img/uploads/' . $this->file . '?w=800&fit=max' . $cropParam,
		];
	}
}
