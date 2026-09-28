<?php

namespace App\Actions\Site;

use App\Models\GridRow;
use App\Models\Project;
use App\Support\GridContext;
use Illuminate\Support\Collection;

/**
 * Project detail: grid rows, downloads and the previous/next project.
 */
class GetProject
{
	public function __construct(private GetNavigation $navigation) {}

	public function execute(Project $project): array
	{
		$project->load(['categoryType', 'images', 'files']);
		[$prev, $next] = $this->browse($project);

		return [
			'project' => $project,
			'rows' => $this->rows($project),
			'prev' => $prev,
			'next' => $next,
		];
	}

	/**
	 * Published rows of the image grid, each as its layout's filled columns.
	 *
	 * @return Collection<int, array{layout: string, columns: array}>
	 */
	private function rows(Project $project): Collection
	{
		$grid = GridContext::for('project');

		return $project->gridRows()
			->published()
			->inArea('main')
			->with('items.media.mediable')
			->get()
			->map(fn (GridRow $row) => ['layout' => $row->layout, 'columns' => $grid->fill($row->layout, $row->items)]);
	}

	/**
	 * Previous and next project in menu order, wrapping around. Legacy quirk
	 * kept: a project that is not in the menu (no detail page) is treated as
	 * the first one (prev = last, next = second).
	 *
	 * @return array{0: ?Project, 1: ?Project}
	 */
	private function browse(Project $project): array
	{
		$ids = $this->navigation->projectIds();
		if (count($ids) < 2) {
			return [null, null];
		}

		$key = (int) array_search($project->id, $ids, true);
		$prevId = $ids[$key - 1] ?? end($ids);
		$nextId = $ids[$key + 1] ?? $ids[0];
		$projects = Project::with('images')->findMany([$prevId, $nextId])->keyBy('id');

		return [$projects[$prevId] ?? null, $projects[$nextId] ?? null];
	}
}
