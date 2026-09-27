<?php

namespace App\Http\Controllers\Api;

use App\Actions\Project\DeleteAction;
use App\Actions\Project\StoreAction;
use App\Actions\Project\UpdateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;

class ProjectController extends Controller
{
	public function index()
	{
		$projects = Project::with('topic')
			->orderByDesc('year')
			->orderByDesc('id')
			->get();

		return ProjectResource::collection($projects);
	}

	public function featured()
	{
		return Project::published()
			->featured()
			->orderBy('title')
			->get(['uuid', 'title', 'slug', 'location'])
			->map(fn ($p) => [
				'uuid' => $p->uuid,
				'title' => $p->title,
				'slug' => $p->slug,
				'location' => $p->location,
			]);
	}

	public function store(StoreProjectRequest $request)
	{
		$project = (new StoreAction)->execute($request->validated());
		return (new ProjectResource($project->load('topic')))->response()->setStatusCode(201);
	}

	public function show(Project $project)
	{
		return new ProjectResource($project->load(['media', 'topic']));
	}

	public function update(UpdateProjectRequest $request, Project $project)
	{
		$project = (new UpdateAction)->execute($project, $request->validated());
		return new ProjectResource($project->load(['media', 'topic']));
	}

	public function toggle(Project $project)
	{
		$project->update(['publish' => !$project->publish]);
		return new ProjectResource($project);
	}

	public function feature(Project $project)
	{
		$project->update(['feature' => !$project->feature]);
		return new ProjectResource($project);
	}

	public function destroy(Project $project)
	{
		(new DeleteAction)->execute($project);
		return response()->json(null, 204);
	}
}
