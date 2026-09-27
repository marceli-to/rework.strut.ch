<?php

namespace App\Actions\Media;

use App\Models\Media;

class ReorderAction
{
	public function execute(array $items): void
	{
		foreach ($items as $item) {
			Media::where('uuid', $item['uuid'])->update(['sort_order' => $item['sort_order']]);
		}
	}
}
