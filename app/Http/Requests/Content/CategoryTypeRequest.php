<?php

namespace App\Http\Requests\Content;

use App\Http\Requests\ContentRequest;
use App\Models\Category;

class CategoryTypeRequest extends ContentRequest
{
	protected bool $withMedia = false;

	protected array $uuidFields = ['category_id' => Category::class];

	protected function fields(): array
	{
		return [
			'category_id' => 'required|string|exists:categories,uuid',
			'name_singular' => 'required|string|max:255',
			'name_plural' => 'required|string|max:255',
			'publish' => 'boolean',
		];
	}

	public function attributes(): array
	{
		return ['category_id' => 'Kategorie', 'name_singular' => 'Name (Einzahl)', 'name_plural' => 'Name (Mehrzahl)'];
	}
}
