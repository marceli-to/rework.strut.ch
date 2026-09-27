<?php

namespace App\Actions\Media;

use App\Models\Media;

class UpdateAction
{
	public function execute(Media $media, array $data): Media
	{
		$media->update($data);

		return $media;
	}
}
