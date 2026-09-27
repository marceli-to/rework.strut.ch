<?php

namespace App\Actions\Grid;

use App\Models\GridItem;
use App\Models\GridRow;

class SetItemAction
{
	/**
	 * Place media or news in a slot (replaces what was there).
	 */
	public function execute(GridRow $row, int $position, array $data): GridItem
	{
		return $row->items()->updateOrCreate(
			['position' => $position],
			['media_id' => $data['media_id'] ?? null, 'news_id' => $data['news_id'] ?? null],
		);
	}
}
