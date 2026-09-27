<?php

namespace App\Http\Controllers\Api;

use App\Actions\Content\DeleteAction;
use App\Actions\Content\ReorderAction;
use App\Actions\Content\SaveAction;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Shared admin CRUD for content modules. Subclasses only declare the model,
 * resource and request (and optionally eager loads / the index query).
 */
abstract class ResourceController extends Controller
{
	/** @var class-string<Model> */
	protected string $model;

	/** @var class-string<\Illuminate\Http\Resources\Json\JsonResource> */
	protected string $resource;

	/** @var class-string<\App\Http\Requests\ContentRequest> */
	protected string $request;

	/** Relations loaded for show/store/update. */
	protected array $with = ['media'];

	/** Relations loaded for the index list. */
	protected array $indexWith = [];

	protected function query(Request $request): Builder
	{
		$query = $this->model::query()->with($this->indexWith);

		return method_exists($this->model, 'scopeOrdered') ? $query->ordered() : $query->latest('id');
	}

	protected function find(string $uuid): Model
	{
		return $this->model::where('uuid', $uuid)->firstOrFail();
	}

	public function index(Request $request)
	{
		return $this->resource::collection($this->query($request)->get());
	}

	public function show(string $uuid)
	{
		return new $this->resource($this->find($uuid)->load($this->with));
	}

	public function store()
	{
		$model = (new SaveAction)->execute(new $this->model, app($this->request)->payload());

		return (new $this->resource($model->load($this->with)))->response()->setStatusCode(201);
	}

	public function update(string $uuid)
	{
		$model = (new SaveAction)->execute($this->find($uuid), app($this->request)->payload());

		return new $this->resource($model->load($this->with));
	}

	public function toggle(string $uuid)
	{
		$model = $this->find($uuid);
		$model->update(['publish' => !$model->publish]);

		return new $this->resource($model);
	}

	public function reorder(Request $request)
	{
		$table = (new $this->model)->getTable();

		$items = $request->validate([
			'items' => 'required|array',
			'items.*.uuid' => "required|string|exists:{$table},uuid",
			'items.*.sort_order' => 'required|integer|min:0',
		])['items'];

		(new ReorderAction)->execute($this->model, $items);

		return response()->json(['message' => 'ok']);
	}

	public function destroy(string $uuid)
	{
		(new DeleteAction)->execute($this->find($uuid));

		return response()->json(null, 204);
	}
}
