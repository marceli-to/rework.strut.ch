<?php

namespace App\Actions\Content;

use Illuminate\Support\Facades\DB;

class ReorderAction
{
	/**
	 * @param class-string<\Illuminate\Database\Eloquent\Model> $model
	 * @param array<int, array{uuid: string, sort_order: int}> $items
	 */
	public function execute(string $model, array $items): void
	{
		DB::transaction(function () use ($model, $items) {
			foreach ($items as $item) {
				$model::where('uuid', $item['uuid'])->update(['sort_order' => $item['sort_order']]);
			}
		});
	}
}
