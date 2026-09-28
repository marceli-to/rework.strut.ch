<?php

namespace App\Actions\Site;

use App\Enums\Competition;
use App\Enums\ProjectStatus;
use App\Models\Category;
use App\Models\Project;
use App\Support\Columns;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

/**
 * Werkliste: all published projects (with or without a detail page), listed
 * by status, year or type. Orders follow the legacy WorksController.
 */
class GetWorks
{
	/**
	 * @return array{status: array, competition: array}
	 *               enum value → ['label' => …, 'projects' => …], empty groups left out
	 */
	public function byStatus(): array
	{
		$byStatus = $this->projects()->get()->groupBy(fn (Project $project) => $project->status->value);

		// Legacy: ordered by status, then each prize sorted by year (stable).
		$byCompetition = Project::published()
			->whereNotNull('competition')
			->orderBy('status')->orderBy('id')
			->get()
			->groupBy(fn (Project $project) => $project->competition->value);

		return [
			'status' => $this->labelled(ProjectStatus::cases(), $byStatus),
			'competition' => $this->labelled(Competition::cases(), $byCompetition->map->sortByDesc('year')),
		];
	}

	/**
	 * @return Collection<int, Collection<int, Collection<int, Project>>> columns → year → projects
	 */
	public function byYear(): Collection
	{
		return Columns::split($this->projects()->get()->groupBy('year'));
	}

	/**
	 * Published categories → published types → projects; empty types and
	 * categories are left out. `withFiles`: only projects with PDF downloads
	 * (Downloads page), files loaded.
	 *
	 * @return Collection<int, Category>
	 */
	public function byType(bool $withFiles = false): Collection
	{
		$projects = fn ($q) => $this->ordered($q->published())
			->when($withFiles, fn ($q) => $q->whereHas('files')->with('files'));

		return Category::published()
			->orderBy('sort_order')->orderBy('id')
			->with(['types' => fn ($q) => $q->published()->with(['projects' => $projects])])
			->get()
			->each(fn (Category $category) => $category->setRelation(
				'types',
				$category->types->filter(fn ($type) => $type->projects->isNotEmpty())->values(),
			))
			->filter(fn (Category $category) => $category->types->isNotEmpty())
			->values();
	}

	private function projects(): Builder
	{
		return $this->ordered(Project::published());
	}

	private function ordered(Builder|Relation $query): Builder|Relation
	{
		return $query->reorder()->orderByDesc('year')->orderBy('name');
	}

	private function labelled(array $cases, Collection $groups): array
	{
		return collect($cases)
			->filter(fn ($case) => $groups->has($case->value))
			->mapWithKeys(fn ($case) => [$case->value => ['label' => $case->label(), 'projects' => $groups[$case->value]->values()]])
			->all();
	}
}
