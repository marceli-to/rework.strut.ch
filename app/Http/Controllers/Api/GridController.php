<?php

namespace App\Http\Controllers\Api;

use App\Actions\Content\ReorderAction;
use App\Actions\Grid\MoveItemAction;
use App\Actions\Grid\SetItemAction;
use App\Actions\Grid\StoreRowAction;
use App\Actions\Grid\UpdateRowAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Grid\GridRequest;
use App\Http\Requests\Grid\ItemRequest;
use App\Http\Requests\Grid\MoveRequest;
use App\Http\Requests\Grid\RowRequest;
use App\Http\Resources\GridMediaResource;
use App\Http\Resources\GridRowResource;
use App\Models\GridRow;
use App\Models\News;
use Illuminate\Http\Request;

/**
 * Shared grid editor API for all grid contexts (project, home).
 * Routes: /api/dashboard/grids/{context}/{owner}/...
 */
class GridController extends Controller
{
	public function show(GridRequest $request)
	{
		return response()->json([
			'config' => $request->context()->toArray(),
			'rows' => GridRowResource::collection($this->rows($request)),
		]);
	}

	/**
	 * What can be placed: media of the context's scope (+ news where allowed).
	 */
	public function options(GridRequest $request)
	{
		$context = $request->context();

		$media = $context->mediaQuery($request->owner())
			->with('mediable')
			->withCount('gridItems')
			->orderBy('mediable_id')
			->orderBy('sort_order')
			->get();

		$acceptsNews = $context->toArray()['accepts_news'];

		return response()->json([
			'media' => GridMediaResource::collection($media),
			'news' => $acceptsNews
				? News::latest('id')->get()->map(fn (News $news) => [
					'uuid' => $news->uuid,
					'title' => $news->title,
					'date_label' => $news->date_label,
					'publish' => $news->publish,
				])
				: [],
		]);
	}

	public function storeRow(RowRequest $request)
	{
		$row = (new StoreRowAction)->execute($request->owner(), $request->validated());

		return (new GridRowResource($row->load('items')))->response()->setStatusCode(201);
	}

	public function updateRow(RowRequest $request)
	{
		$row = (new UpdateRowAction)->execute($request->context(), $request->row(), $request->validated());

		return new GridRowResource($this->loadItems($row));
	}

	public function destroyRow(GridRequest $request)
	{
		$request->row()->delete();

		return response()->json(null, 204);
	}

	public function reorderRows(GridRequest $request)
	{
		$owner = $request->owner();

		$items = $request->validate([
			'items' => 'required|array',
			'items.*.uuid' => ['required', 'string', fn ($attribute, $value, $fail) => $owner->gridRows()->where('uuid', $value)->exists() ?: $fail('Ungültige Zeile.')],
			'items.*.sort_order' => 'required|integer|min:0',
		])['items'];

		(new ReorderAction)->execute(GridRow::class, $items);

		return response()->json(['message' => 'ok']);
	}

	public function setItem(ItemRequest $request)
	{
		$row = $request->row();
		(new SetItemAction)->execute($row, (int) $request->route('position'), $request->payload());

		return new GridRowResource($this->loadItems($row));
	}

	public function moveItem(MoveRequest $request)
	{
		$from = $request->row($request->input('from_row'));
		$item = $from->items()->where('position', $request->input('from_position'))->firstOrFail();

		(new MoveItemAction)->execute($item, $request->row($request->input('to_row')), (int) $request->input('to_position'));

		return response()->json(['rows' => GridRowResource::collection($this->rows($request))]);
	}

	public function destroyItem(GridRequest $request)
	{
		$row = $request->row();
		$row->items()->where('position', (int) $request->route('position'))->delete();

		return new GridRowResource($this->loadItems($row));
	}

	protected function rows(GridRequest $request)
	{
		return $request->owner()->gridRows()->get()->each(fn (GridRow $row) => $this->loadItems($row));
	}

	protected function loadItems(GridRow $row): GridRow
	{
		return $row->load(['items.media.mediable', 'items.news']);
	}
}
