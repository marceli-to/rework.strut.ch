<?php

namespace App\Actions\Site;

use App\Models\GridRow;
use App\Models\Page;
use App\Support\GridContext;
use Illuminate\Support\Collection;

/**
 * Homepage: highlight slides (shuffled per request, as legacy) and the grid.
 */
class GetHome
{
	public function execute(): array
	{
		$page = Page::findByKey('home');
		$rows = $page->gridRows()
			->published()
			->with(['items.media.mediable', 'items.news' => fn ($q) => $q->published()->with('images')])
			->get();

		return [
			'page' => $page,
			'slides' => $this->slides($rows->where('area', 'highlight')),
			'rows' => $this->rows($rows->where('area', 'main')),
		];
	}

	/**
	 * @return Collection<int, \App\Models\GridItem> items with a project image or video
	 */
	private function slides(Collection $rows): Collection
	{
		return $rows->flatMap->items
			->filter(fn ($item) => $item->media?->mediable)
			->shuffle()
			->values();
	}

	/**
	 * @return Collection<int, array{layout: string, columns: array}>
	 */
	private function rows(Collection $rows): Collection
	{
		$grid = GridContext::for('home');

		return $rows->values()->map(fn (GridRow $row) => ['layout' => $row->layout, 'columns' => $grid->fill($row->layout, $row->items)]);
	}
}
