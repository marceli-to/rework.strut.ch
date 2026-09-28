<?php

namespace App\Actions\Site;

use App\Enums\Competition;
use App\Enums\ProjectStatus;
use App\Models\Category;
use App\Models\LegacyMap;
use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Content of the eight Werkliste PDFs, as the legacy PdfController and
 * resources/views/web/pdf/*: sections (underlined title) of groups (optional
 * bold heading, lines, optional blank line after).
 */
class GetWorksPdf
{
	public const VARIANTS = [
		'gesamt' => ['title' => 'Werkliste Gesamt', 'file' => 'gesamt'],
		'wohnen' => ['title' => 'Werkliste Wohnen', 'file' => 'wohnen', 'category' => 1],
		'gewerbe' => ['title' => 'Werkliste Gewerbe', 'file' => 'gewerbe', 'category' => 2],
		'oeffentlich' => ['title' => 'Werkliste Öffentlich', 'file' => 'oeffentlich', 'category' => 3],
		'wettbewerb' => ['title' => 'Werkliste Wettbewerbe', 'file' => 'wettbewerbe'],
		'status' => ['title' => 'Werkliste nach Status', 'file' => 'status'],
		'jahr' => ['title' => 'Werkliste nach Jahr', 'file' => 'jahr'],
		'typ' => ['title' => 'Werkliste nach Typ', 'file' => 'typ'],
	];

	/**
	 * Status as written after a project in the year and type lists (legacy config/status.php).
	 */
	private const STATUS_INLINE = [
		'executed' => 'ausgeführt',
		'planned' => 'in Planung',
		'study' => 'Studie',
	];

	/**
	 * @return array{title: string, sections: array<int, array{title: string, groups: array}>}
	 */
	public function execute(string $variant): array
	{
		$spec = self::VARIANTS[$variant];

		$sections = match ($variant) {
			'gesamt' => [...$this->byCategory($this->categories(), withStatus: false, skipWithoutTypes: true), ...$this->competition('Wettbewerbe')],
			'wohnen', 'gewerbe', 'oeffentlich' => $this->byType($this->categories($spec['category'])->first(), skipEmpty: $variant === 'wohnen'),
			'wettbewerb' => $this->competitionSections(),
			'status' => $this->byStatus(),
			'jahr' => $this->byYear(),
			'typ' => $this->byCategory($this->categories(), withStatus: true, skipWithoutTypes: false),
		};

		return ['title' => $spec['title'], 'sections' => $sections];
	}

	/**
	 * Published categories with published types and published projects;
	 * `legacyId`: only the category imported from that legacy id.
	 */
	private function categories(?int $legacyId = null): Collection
	{
		return Category::published()
			->when($legacyId, fn (Builder $q) => $q->whereKey(LegacyMap::where('legacy_table', 'categories')->where('legacy_id', $legacyId)->value('model_id')))
			->orderBy('sort_order')->orderBy('id')
			->with(['types' => fn ($q) => $q->published()->with(['projects' => fn ($q) => $this->ordered($q->published())])])
			->get();
	}

	private function ordered(Builder|\Illuminate\Database\Eloquent\Relations\Relation $query): Builder|\Illuminate\Database\Eloquent\Relations\Relation
	{
		return $query->reorder()->orderByDesc('year')->orderBy('name');
	}

	/**
	 * Gesamt / Typ: one section per category; with type headings where the
	 * category shows types (and the type has projects), else plain lines.
	 */
	private function byCategory(Collection $categories, bool $withStatus, bool $skipWithoutTypes): array
	{
		$line = fn (Project $p) => "{$p->name}, {$p->location} – {$p->year}" . ($withStatus ? ', ' . self::STATUS_INLINE[$p->status->value] : '');

		return $categories
			->reject(fn (Category $category) => $skipWithoutTypes && $category->types->isEmpty())
			->map(fn (Category $category) => [
				'title' => $category->name,
				'groups' => $category->types->map(fn ($type) => $category->show_types && $type->projects->isNotEmpty()
					? ['heading' => $type->name_plural, 'lines' => $type->projects->map($line)->all(), 'break' => true]
					: ['heading' => null, 'lines' => $type->projects->map($line)->all(), 'break' => false])->all(),
			])->values()->all();
	}

	/**
	 * Wohnen / Gewerbe / Öffentlich: one section per type. Legacy "Wohnen"
	 * leaves out types without projects, the other two do not.
	 */
	private function byType(?Category $category, bool $skipEmpty): array
	{
		if (!$category) {
			return [];
		}

		return $category->types
			->reject(fn ($type) => $skipEmpty && $type->projects->isEmpty())
			->map(fn ($type) => [
				'title' => $type->name_plural,
				'groups' => [['heading' => null, 'lines' => $type->projects->map(fn (Project $p) => "{$p->name}, {$p->location} – {$p->year}")->all(), 'break' => false]],
			])->values()->all();
	}

	/**
	 * Competition projects by prize, ordered by status (as legacy, no year sort).
	 */
	private function competitionGroups(): Collection
	{
		$projects = Project::published()->whereNotNull('competition')->with('categoryType')->orderBy('status')->orderBy('id')->get()
			->groupBy(fn (Project $p) => $p->competition->value);

		return collect(Competition::cases())
			->filter(fn (Competition $prize) => $projects->has($prize->value))
			->map(fn (Competition $prize) => [
				'prize' => $prize,
				'lines' => $projects[$prize->value]->map(fn (Project $p) => "{$p->name}, {$p->location} – {$p->categoryType->name_singular}, {$p->year}")->all(),
			])->values();
	}

	/**
	 * Gesamt: one "Wettbewerbe" section with a bold heading per prize; a blank
	 * line follows 1. and 2. Preis, not "Andere".
	 */
	private function competition(string $title): array
	{
		$groups = $this->competitionGroups();

		return $groups->isEmpty() ? [] : [[
			'title' => $title,
			'groups' => $groups->map(fn ($group) => [
				'heading' => $group['prize']->label(),
				'lines' => $group['lines'],
				'break' => $group['prize'] !== Competition::Other,
			])->all(),
		]];
	}

	/**
	 * Wettbewerbe: one section per prize.
	 */
	private function competitionSections(): array
	{
		return $this->competitionGroups()->map(fn ($group) => [
			'title' => $group['prize']->label(),
			'groups' => [['heading' => null, 'lines' => $group['lines'], 'break' => false]],
		])->all();
	}

	/**
	 * Status: Ausgeführt, In Planung, Studie (no competition section, as legacy).
	 */
	private function byStatus(): array
	{
		$projects = $this->ordered(Project::published()->with('categoryType'))->get()->groupBy(fn (Project $p) => $p->status->value);

		return collect(ProjectStatus::cases())
			->filter(fn (ProjectStatus $status) => $projects->has($status->value))
			->map(fn (ProjectStatus $status) => [
				'title' => $status->label(),
				'groups' => [['heading' => null, 'lines' => $projects[$status->value]->map(fn (Project $p) => "{$p->name}, {$p->location} – {$p->categoryType->name_singular}, {$p->year}")->all(), 'break' => false]],
			])->values()->all();
	}

	private function byYear(): array
	{
		return $this->ordered(Project::published()->with('categoryType'))->get()
			->groupBy('year')
			->map(fn (Collection $projects, int $year) => [
				'title' => (string) $year,
				'groups' => [['heading' => null, 'lines' => $projects->map(fn (Project $p) => "{$p->name}, {$p->location} – {$p->categoryType->name_singular}, " . self::STATUS_INLINE[$p->status->value])->all(), 'break' => false]],
			])->values()->all();
	}
}
