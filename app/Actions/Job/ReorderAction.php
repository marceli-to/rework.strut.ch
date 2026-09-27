<?php

namespace App\Actions\Job;

use App\Models\JobListing;

class ReorderAction
{
	public function execute(array $items): void
	{
		foreach ($items as $item) {
			JobListing::where('uuid', $item['uuid'])->update(['sort_order' => $item['sort_order']]);
		}
	}
}
