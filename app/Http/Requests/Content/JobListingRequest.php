<?php

namespace App\Http\Requests\Content;

use App\Http\Requests\ContentRequest;

class JobListingRequest extends ContentRequest
{
	protected function fields(): array
	{
		return [
			'title' => 'required|string|max:255',
			'lead' => 'nullable|string|max:255',
			'info' => 'nullable|string',
			'publish' => 'boolean',
		];
	}

	public function attributes(): array
	{
		return ['title' => 'Titel', 'lead' => 'Lead', 'info' => 'Info'];
	}
}
