<?php

namespace App\Actions\Landing;

use App\Models\LandingSlide;

class ReorderAction
{
	public function execute(array $items): void
	{
		foreach ($items as $item) {
			LandingSlide::where('uuid', $item['uuid'])->update([
				'sort_order' => $item['sort_order'],
			]);
		}
	}
}
