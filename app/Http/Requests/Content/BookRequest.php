<?php

namespace App\Http\Requests\Content;

use App\Http\Requests\ContentRequest;

class BookRequest extends ContentRequest
{
	protected function fields(): array
	{
		return [
			'title' => 'required|string|max:255',
			'description' => 'nullable|string',
			'info' => 'nullable|string',
			'url' => ['nullable', 'string', 'max:255', function ($attribute, $value, $fail) {
				if (!filter_var($value, FILTER_VALIDATE_URL) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
					$fail('Das Feld Bestellung muss eine gültige URL oder E-Mail-Adresse sein.');
				}
			}],
			'publish' => 'boolean',
		];
	}

	public function attributes(): array
	{
		return ['title' => 'Titel', 'description' => 'Angaben', 'info' => 'Beschreibung', 'url' => 'Bestellung'];
	}
}
