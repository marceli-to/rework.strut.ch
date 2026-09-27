<?php

namespace App\Actions\Landing;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\LandingSlide;

class StoreAction
{
	public function execute(array $data): LandingSlide
	{
		$media = $data['media'] ?? [];
		unset($data['media']);

		$slide = LandingSlide::create($data);

		if (!empty($media)) {
			(new AttachMediaAction)->execute($media, $slide);
		}

		return $slide;
	}
}
