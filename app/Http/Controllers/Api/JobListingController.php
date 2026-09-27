<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Content\JobListingRequest;
use App\Http\Resources\JobListingResource;
use App\Models\JobListing;

class JobListingController extends ResourceController
{
	protected string $model = JobListing::class;
	protected string $resource = JobListingResource::class;
	protected string $request = JobListingRequest::class;
}
