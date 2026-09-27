<?php

namespace App\Actions\Job;

use App\Models\JobListing;

class StoreAction
{
	public function execute(array $data): JobListing
	{
		return JobListing::create($data);
	}
}
