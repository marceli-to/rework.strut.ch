<?php

namespace App\Actions\Grid;

use App\Models\GridRow;
use Illuminate\Database\Eloquent\Model;

class StoreRowAction
{
	public function execute(Model $owner, array $data): GridRow
	{
		$sortOrder = ($owner->gridRows()->where('area', $data['area'])->max('sort_order') ?? -1) + 1;

		return $owner->gridRows()->create([
			'area' => $data['area'],
			'layout' => $data['layout'],
			'publish' => $data['publish'] ?? true,
			'sort_order' => $sortOrder,
		]);
	}
}
