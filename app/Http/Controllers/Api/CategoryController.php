<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Content\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends ResourceController
{
	protected string $model = Category::class;
	protected string $resource = CategoryResource::class;
	protected string $request = CategoryRequest::class;

	protected array $with = ['types'];
	protected array $indexWith = ['types'];
}
