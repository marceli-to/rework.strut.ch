<?php

namespace App\Actions\Grid;

use App\Models\GridRow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StoreRowAction
{
	// new rows go to the top of their area
	public function execute(Model $owner, array $data): GridRow
	{
		return DB::transaction(function () use ($owner, $data) {
			$owner->gridRows()->where('area', $data['area'])->increment('sort_order');

			return $owner->gridRows()->create([
				'area' => $data['area'],
				'layout' => $data['layout'],
				'publish' => $data['publish'] ?? true,
				'sort_order' => 0,
			]);
		});
	}
}
