<?php

namespace App\Actions\Project;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\Project;
use App\Models\Topic;

class UpdateAction
{
	public function execute(Project $project, array $data): Project
	{
		$media = $data['media'] ?? [];
		unset($data['media']);

		if (empty($data['slug'])) {
			unset($data['slug']);
		}

		if (!empty($data['topic_id'])) {
			$topic = Topic::where('uuid', $data['topic_id'])->first();
			$data['topic_id'] = $topic?->id;
		} else {
			$data['topic_id'] = null;
		}

		$project->update($data);

		if (!empty($media)) {
			(new AttachMediaAction)->execute($media, $project);
		}

		return $project->fresh();
	}
}
