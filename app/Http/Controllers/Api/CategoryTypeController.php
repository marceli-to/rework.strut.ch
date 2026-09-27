<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Content\CategoryTypeRequest;
use App\Http\Resources\CategoryTypeResource;
use App\Models\CategoryType;

class CategoryTypeController extends ResourceController
{
	protected string $model = CategoryType::class;
	protected string $resource = CategoryTypeResource::class;
	protected string $request = CategoryTypeRequest::class;

	protected array $with = ['category'];
	protected array $indexWith = ['category'];
}
