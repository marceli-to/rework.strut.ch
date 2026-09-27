<?php

namespace App\Actions\Landing;

use App\Actions\Media\DeleteAction as DeleteMediaAction;
use App\Models\LandingSlide;

class DeleteAction
{
	public function execute(LandingSlide $slide): void
	{
		$deleteMedia = new DeleteMediaAction;

		foreach ($slide->media as $media) {
			$deleteMedia->execute($media);
		}

		$slide->delete();
	}
}
