<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Content\ProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProjectController extends ResourceController
{
	protected string $model = Project::class;
	protected string $resource = ProjectResource::class;
	protected string $request = ProjectRequest::class;

	protected array $with = ['media', 'categoryType.category'];
	protected array $indexWith = ['categoryType.category'];

	protected function query(Request $request): Builder
	{
		return Project::query()
			->with($this->indexWith)
			->join('category_types', 'category_types.id', '=', 'projects.category_type_id')
			->join('categories', 'categories.id', '=', 'category_types.category_id')
			->orderBy('categories.sort_order')
			->orderBy('category_types.sort_order')
			->orderBy('projects.sort_order')
			->select('projects.*');
	}
}
