<?php

namespace App\Http\Controllers\Api;

use App\Actions\Job\DeleteAction;
use App\Actions\Job\ReorderAction;
use App\Actions\Job\StoreAction;
use App\Actions\Job\UpdateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Job\ReorderJobRequest;
use App\Http\Requests\Job\StoreJobRequest;
use App\Http\Requests\Job\UpdateJobRequest;
use App\Http\Resources\JobListingResource;
use App\Models\JobListing;

class JobController extends Controller
{
	public function index()
	{
		return JobListingResource::collection(
			JobListing::orderBy('sort_order')->get()
		);
	}

	public function store(StoreJobRequest $request)
	{
		$job = (new StoreAction)->execute($request->validated());
		return (new JobListingResource($job))->response()->setStatusCode(201);
	}

	public function show(JobListing $job)
	{
		return new JobListingResource($job);
	}

	public function update(UpdateJobRequest $request, JobListing $job)
	{
		$job = (new UpdateAction)->execute($job, $request->validated());
		return new JobListingResource($job);
	}

	public function toggle(JobListing $job)
	{
		$job->update(['publish' => !$job->publish]);
		return new JobListingResource($job);
	}

	public function destroy(JobListing $job)
	{
		(new DeleteAction)->execute($job);
		return response()->json(null, 204);
	}

	public function reorder(ReorderJobRequest $request)
	{
		(new ReorderAction)->execute($request->validated('items'));
		return response()->json(['message' => 'ok']);
	}
}
