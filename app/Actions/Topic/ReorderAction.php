<?php

namespace App\Actions\Topic;

use App\Models\Topic;

class ReorderAction
{
	public function execute(array $items): void
	{
		foreach ($items as $item) {
			Topic::where('uuid', $item['uuid'])->update(['sort_order' => $item['sort_order']]);
		}
	}
}
