<?php

namespace App\Actions\Project;

use App\Actions\Media\DeleteAction as DeleteMediaAction;
use App\Models\Project;

class DeleteAction
{
	public function execute(Project $project): void
	{
		$deleteMedia = new DeleteMediaAction;

		foreach ($project->media as $media) {
			$deleteMedia->execute($media);
		}

		$project->delete();
	}
}
