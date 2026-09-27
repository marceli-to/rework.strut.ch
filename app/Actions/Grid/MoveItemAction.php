<?php

namespace App\Actions\Grid;

use App\Models\GridItem;
use App\Models\GridRow;
use Illuminate\Support\Facades\DB;

class MoveItemAction
{
	/**
	 * Move an item to another slot (same or other row of the same owner),
	 * swapping with the item already there.
	 */
	public function execute(GridItem $item, GridRow $targetRow, int $targetPosition): void
	{
		DB::transaction(function () use ($item, $targetRow, $targetPosition) {
			$target = $targetRow->items()->where('position', $targetPosition)->first();
			$from = ['grid_row_id' => $item->grid_row_id, 'position' => $item->position];

			// park the moving item to avoid the (row, position) unique constraint
			$item->update(['position' => 1000000 + $item->id]);
			$target?->update($from);
			$item->update(['grid_row_id' => $targetRow->id, 'position' => $targetPosition]);
		});
	}
}
