<?php

namespace App\Http\Resources;

use App\Support\MediaUrls;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'uuid' => $this->uuid,
			'collection' => $this->collection,
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
			...MediaUrls::for($this->file, $this->mime_type, $this->crop),
		];
	}
}
