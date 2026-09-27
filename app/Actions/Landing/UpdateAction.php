<?php

namespace App\Actions\Landing;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\LandingSlide;

class UpdateAction
{
	public function execute(LandingSlide $slide, array $data): LandingSlide
	{
		$media = $data['media'] ?? [];
		unset($data['media']);

		$slide->update($data);

		if (!empty($media)) {
			(new AttachMediaAction)->execute($media, $slide);
		}

		return $slide->fresh();
	}
}
