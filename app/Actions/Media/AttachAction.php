<?php

namespace App\Actions\Media;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AttachAction
{
	public function execute(array $mediaItems, Model $parent): void
	{
		$maxSort = $parent->media()->max('sort_order') ?? -1;

		foreach ($mediaItems as $item) {
			$tempPath = 'temp/' . $item['file'];

			if (!Storage::disk('public')->exists($tempPath)) {
				continue;
			}

			Storage::disk('public')->move($tempPath, 'uploads/' . $item['file']);

			$maxSort++;
			$parent->media()->create([
				'uuid' => $item['uuid'],
				'collection' => $item['collection'] ?? 'images',
				'file' => $item['file'],
				'original_name' => $item['original_name'],
				'mime_type' => $item['mime_type'],
				'size' => $item['size'],
				'width' => $item['width'] ?? null,
				'height' => $item['height'] ?? null,
				'alt' => $item['alt'] ?? null,
				'caption' => $item['caption'] ?? null,
				'crop' => $item['crop'] ?? null,
				'variant' => $item['variant'] ?? 'desktop',
				'is_teaser' => $item['is_teaser'] ?? false,
				'is_og' => $item['is_og'] ?? false,
				'sort_order' => $maxSort,
			]);
		}
	}
}
