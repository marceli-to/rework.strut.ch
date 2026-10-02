<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Content\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class CategoryController extends ResourceController
{
	protected string $model = Category::class;
	protected string $resource = CategoryResource::class;
	protected string $request = CategoryRequest::class;

	protected array $with = ['types'];
	protected array $indexWith = ['types'];

	// projects.category_type_id restricts deletes; the types would cascade
	protected function preventDelete(Model $model): ?string
	{
		$count = $model->projects()->count();

		return $count ? "Die Kategorie enthält noch {$count} " . ($count === 1 ? 'Projekt' : 'Projekte') . '. Bitte zuerst die Projekte verschieben oder löschen.' : null;
	}
}
