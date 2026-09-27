<?php

namespace App\Actions\Atelier;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\AtelierPage;

class UpdateAction
{
	public function execute(AtelierPage $page, array $data): AtelierPage
	{
		$media = $data['media'] ?? [];
		unset($data['media']);

		$page->update($data);

		if (!empty($media)) {
			(new AttachMediaAction)->execute($media, $page);
		}

		return $page->fresh();
	}
}
