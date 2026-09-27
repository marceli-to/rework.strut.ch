<?php

namespace App\Actions\Media;

use App\Models\Media;

class SetTeaserAction
{
	public function execute(Media $media): Media
	{
		$wasTeaser = $media->is_teaser;

		// Unset all teasers for the same entity
		Media::where('mediable_type', $media->mediable_type)
			->where('mediable_id', $media->mediable_id)
			->update(['is_teaser' => false]);

		// Toggle: only set if it wasn't already the teaser
		if (!$wasTeaser) {
			$media->update(['is_teaser' => true]);
		}

		return $media->refresh();
	}
}
