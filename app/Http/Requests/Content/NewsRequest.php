<?php

namespace App\Http\Requests\Content;

use App\Http\Requests\ContentRequest;

class NewsRequest extends ContentRequest
{
	protected function fields(): array
	{
		return [
			'date_label' => 'nullable|string|max:255',
			'title' => 'required|string|max:255',
			'subtitle' => 'nullable|string|max:255',
			'text' => 'nullable|string',
			'link_url' => 'nullable|url|max:255',
			'link_label' => 'nullable|string|max:255',
			'publish' => 'boolean',
		];
	}

	public function attributes(): array
	{
		return [
			'date_label' => 'Datum',
			'title' => 'Titel',
			'subtitle' => 'Untertitel',
			'text' => 'Text',
			'link_url' => 'Link',
			'link_label' => 'Linktext',
		];
	}
}
