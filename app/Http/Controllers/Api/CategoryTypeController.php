<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Content\CategoryTypeRequest;
use App\Http\Resources\CategoryTypeResource;
use App\Models\CategoryType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CategoryTypeController extends ResourceController
{
	protected string $model = CategoryType::class;
	protected string $resource = CategoryTypeResource::class;
	protected string $request = CategoryTypeRequest::class;

	protected array $with = ['category'];
	protected array $indexWith = ['category'];

	protected function query(Request $request): Builder
	{
		return parent::query($request)
			->when($request->query('category'), fn (Builder $q, string $uuid) => $q->whereHas('category', fn (Builder $c) => $c->where('uuid', $uuid)));
	}

	protected function preventDelete(Model $model): ?string
	{
		$count = $model->projects()->count();

		return $count ? "Der Typ enthält noch {$count} " . ($count === 1 ? 'Projekt' : 'Projekte') . '. Bitte zuerst die Projekte verschieben oder löschen.' : null;
	}
}
