<?php

namespace App\Http\Controllers;

use App\Models\Project;

class ProjectController extends Controller
{
	public function featured()
	{
		$projects = Project::published()
			->featured()
			->with('teaser')
			->orderByDesc('year')
			->orderByDesc('id')
			->get();

		return view('pages.projects.index', compact('projects'));
	}

	public function worklist()
	{
		$projects = Project::published()
			->orderByDesc('year')
			->orderByDesc('id')
			->get();

		return view('pages.projects.worklist', compact('projects'));
	}

	public function images(Project $project, string $context)
	{
		abort_unless($project->detail, 404);
		$project->load('media', 'topic');

		[$prev, $next] = $this->siblings($project, $context);
		$canonical = route('page.project.worklist.images', $project->slug);

		return view('pages.projects.images', compact('project', 'prev', 'next', 'context', 'canonical'));
	}

	public function text(Project $project, string $context)
	{
		abort_unless($project->detail, 404);
		$project->load('media', 'topic');

		[$prev, $next] = $this->siblings($project, $context);
		$canonical = route('page.project.worklist.text', $project->slug);

		return view('pages.projects.text', compact('project', 'prev', 'next', 'context', 'canonical'));
	}

	protected function siblings(Project $project, string $context): array
	{
		$query = Project::published()->detailed();

		if ($context === 'featured') {
			$query->featured();
		}

		$query->orderByDesc('year')->orderByDesc('id');

		$siblings = $query->get(['id', 'slug', 'title']);
		$count = $siblings->count();

		if ($count <= 1) {
			return [null, null];
		}

		$index = $siblings->search(fn ($p) => $p->id === $project->id);

		$prev = $siblings[($index - 1 + $count) % $count];
		$next = $siblings[($index + 1) % $count];

		return [$prev, $next];
	}
}
