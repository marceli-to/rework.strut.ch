<?php

namespace App\Support;

use App\Models\Media;
use App\Models\Page;
use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Resolved view on config/grids.php for one context ('project', 'home').
 */
class GridContext
{
	public function __construct(
		public readonly string $key,
		protected array $config,
		protected array $layouts,
	) {}

	public static function for(string $key): self
	{
		$config = config("grids.contexts.$key");

		if (!$config) {
			throw new InvalidArgumentException("Unknown grid context [$key].");
		}

		return new self($key, $config, config("grids.layouts.$key", []));
	}

	public static function exists(string $key): bool
	{
		return config("grids.contexts.$key") !== null;
	}

	/**
	 * Owner of the grid: a project (by uuid) or the page with the context key.
	 */
	public function owner(string $identifier): Model
	{
		return $this->key === 'project'
			? Project::where('uuid', $identifier)->firstOrFail()
			: Page::where('key', $this->key)->firstOrFail();
	}

	public function areas(): array
	{
		return array_keys($this->config['areas']);
	}

	public function area(string $area): array
	{
		return $this->config['areas'][$area] ?? throw new InvalidArgumentException("Unknown area [$area].");
	}

	public function layoutsFor(string $area): array
	{
		return $this->config['areas'][$area]['layouts'] ?? [];
	}

	public function allows(string $area, string $layout): bool
	{
		return in_array($layout, $this->layoutsFor($area), true);
	}

	/**
	 * Slot cells in position order (spacers skipped), or null for unlimited layouts.
	 *
	 * @return array<int, array{size: string, news: bool}>|null
	 */
	public function cells(string $layout): ?array
	{
		$spec = $this->layouts[$layout] ?? throw new InvalidArgumentException("Unknown layout [$layout].");

		if (array_key_exists('slots', $spec) && $spec['slots'] === null) {
			return null;
		}

		$cells = [];
		foreach ($spec['columns'] as $column) {
			foreach ($column['cells'] as $cell) {
				if ($cell['size'] !== 'spacer') {
					$cells[] = $cell;
				}
			}
		}

		return $cells;
	}

	public function hasPosition(string $layout, int $position): bool
	{
		$cells = $this->cells($layout);

		return $position >= 0 && ($cells === null || $position < count($cells));
	}

	public function acceptsNews(string $layout, int $position): bool
	{
		$cells = $this->cells($layout);

		return $cells !== null && ($cells[$position]['news'] ?? false);
	}

	/**
	 * Media that may be placed in this context's grid.
	 */
	public function mediaQuery(Model $owner): Builder
	{
		$query = Media::query()
			->where('collection', 'images')
			->where(fn (Builder $q) => $q->where('mime_type', 'like', 'image/%')->orWhere('mime_type', 'like', 'video/%'));

		return match ($this->config['media']) {
			'own' => $query->where('mediable_type', $owner->getMorphClass())->where('mediable_id', $owner->getKey()),
			'published_projects' => $query->where('mediable_type', 'project')
				->whereIn('mediable_id', Project::published()->select('id')),
		};
	}

	/**
	 * Everything the admin editor needs to render this context.
	 */
	public function toArray(): array
	{
		$sizes = config('grids.sizes');

		return [
			'key' => $this->key,
			'label' => $this->config['label'],
			'accepts_news' => collect($this->layouts)->contains(fn ($l, $key) => collect($this->cells($key) ?? [])->contains('news', true)),
			'areas' => collect($this->config['areas'])->map(fn ($area, $key) => [
				'key' => $key,
				'label' => $area['label'],
				'max_rows' => $area['max_rows'] ?? null,
				'layouts' => $area['layouts'],
			])->values(),
			'layouts' => collect($this->layouts)->map(function ($spec, $key) use ($sizes) {
				$position = 0;
				$columns = [];

				foreach ($spec['columns'] as $column) {
					$cells = [];
					foreach ($column['cells'] as $cell) {
						$cells[] = [
							...$cell,
							'ratio' => $sizes[$cell['size']] ?? null,
							'position' => $cell['size'] === 'spacer' ? null : $position++,
						];
					}
					$columns[] = ['fr' => $column['fr'], 'cells' => $cells];
				}

				return [
					'key' => $key,
					'label' => $spec['label'],
					'slots' => ($cells = $this->cells($key)) === null ? null : count($cells),
					'columns' => $columns,
				];
			})->values(),
		];
	}
}
