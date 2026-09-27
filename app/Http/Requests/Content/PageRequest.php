<?php

namespace App\Http\Requests\Content;

use App\Http\Requests\ContentRequest;

class PageRequest extends ContentRequest
{
	protected function fields(): array
	{
		return [
			'title' => 'required|string|max:255',
			'text' => 'nullable|string',
			'meta_description' => 'nullable|string|max:255',
			'publish' => 'boolean',
		];
	}

	public function attributes(): array
	{
		return ['title' => 'Titel', 'text' => 'Text', 'meta_description' => 'Meta Description'];
	}
}
