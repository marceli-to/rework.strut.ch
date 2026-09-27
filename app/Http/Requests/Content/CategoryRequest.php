<?php

namespace App\Http\Requests\Content;

use App\Http\Requests\ContentRequest;

class CategoryRequest extends ContentRequest
{
	protected bool $withMedia = false;

	protected function fields(): array
	{
		return [
			'name' => 'required|string|max:255',
			'show_types' => 'boolean',
			'publish' => 'boolean',
		];
	}

	public function attributes(): array
	{
		return ['name' => 'Name', 'show_types' => 'Typen anzeigen'];
	}
}
