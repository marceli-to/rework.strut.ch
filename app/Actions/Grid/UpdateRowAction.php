<?php

namespace App\Actions\Grid;

use App\Models\GridRow;
use App\Support\GridContext;
use Illuminate\Support\Facades\DB;

class UpdateRowAction
{
	/**
	 * Changing the layout keeps items whose position still exists (and whose
	 * type is still accepted) and removes the rest.
	 */
	public function execute(GridContext $context, GridRow $row, array $data): GridRow
	{
		return DB::transaction(function () use ($context, $row, $data) {
			$row->update($data);

			$row->items()->get()
				->reject(fn ($item) => $context->hasPosition($row->layout, $item->position)
					&& (!$item->news_id || $context->acceptsNews($row->layout, $item->position)))
				->each->delete();

			return $row->fresh();
		});
	}
}
