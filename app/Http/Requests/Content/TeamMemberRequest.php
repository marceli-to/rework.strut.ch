<?php

namespace App\Http\Requests\Content;

use App\Http\Requests\ContentRequest;

class TeamMemberRequest extends ContentRequest
{
	protected function fields(): array
	{
		return [
			'firstname' => 'required|string|max:255',
			'lastname' => 'required|string|max:255',
			'role' => 'nullable|string|max:255',
			'position' => 'nullable|string|max:255',
			'phone' => 'nullable|string|max:255',
			'email' => 'nullable|email|max:255',
			'cv' => 'nullable|string',
			'publish' => 'boolean',
		];
	}

	public function attributes(): array
	{
		return [
			'firstname' => 'Vorname',
			'lastname' => 'Nachname',
			'role' => 'Funktion',
			'position' => 'Position',
			'phone' => 'Telefon',
			'email' => 'E-Mail',
			'cv' => 'Lebenslauf',
		];
	}
}
