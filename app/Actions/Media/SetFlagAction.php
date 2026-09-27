<?php

namespace App\Actions\Media;

use App\Models\Media;

/**
 * Toggle a "one per collection" flag (is_teaser, is_og) on a media item.
 */
class SetFlagAction
{
	public const FLAGS = ['is_teaser', 'is_og'];

	public function execute(Media $media, string $flag): Media
	{
		$wasSet = $media->{$flag};

		Media::where('mediable_type', $media->mediable_type)
			->where('mediable_id', $media->mediable_id)
			->where('collection', $media->collection)
			->update([$flag => false]);

		if (!$wasSet) {
			$media->update([$flag => true]);
		}

		return $media->refresh();
	}
}
