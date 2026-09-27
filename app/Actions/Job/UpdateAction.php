<?php

namespace App\Actions\Job;

use App\Models\JobListing;

class UpdateAction
{
	public function execute(JobListing $job, array $data): JobListing
	{
		$job->update($data);
		return $job;
	}
}
