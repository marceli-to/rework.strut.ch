<?php

namespace App\Actions\Job;

use App\Models\JobListing;

class DeleteAction
{
	public function execute(JobListing $job): void
	{
		$job->delete();
	}
}
